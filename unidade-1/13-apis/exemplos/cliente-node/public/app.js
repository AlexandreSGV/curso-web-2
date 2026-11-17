const API = 'http://127.0.0.1:8000/api';
const element = id => document.getElementById(id);
const login = element('login');
const form = element('book-form');
const list = element('books');
const details = element('details');

let token = null;
let user = null;
let editingId = null;
let canWrite = false;

function message(text, error = false) {
    element('message').textContent = text;
    element('message').dataset.error = error;
}

async function api(path, method = 'GET', data) {
    const headers = { Accept: 'application/json' };
    if (token) headers.Authorization = 'Bearer ' + token;
    if (data !== undefined) headers['Content-Type'] = 'application/json';

    const response = await fetch(API + path, {
        method, headers, body: data === undefined ? undefined : JSON.stringify(data),
    });

    if (response.status === 204) return;
    const result = await response.json();

    if (!response.ok) {
        if (response.status === 401) {
            closeSession();
            throw new Error('Entre novamente para continuar.');
        }
        const errors = Object.values(result.errors ?? {}).flat();
        throw new Error(errors.length ? errors.join(' ') : result.message || 'A API recusou a operação.');
    }

    return result;
}

async function run(action) {
    message('');
    try {
        await action();
    } catch (error) {
        message(error.message, true);
    }
}

function closeSession() {
    token = null;
    user = null;
    canWrite = false;
    editingId = null;
    login.reset();
    form.reset();
    form.hidden = true;
    details.hidden = true;
    details.replaceChildren();
    list.replaceChildren();
    login.hidden = false;
    element('library').hidden = true;
}

function action(label, handler) {
    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = label;
    button.addEventListener('click', () => run(handler));
    return button;
}

async function loadBooks() {
    const { data } = await api('/books');
    list.replaceChildren();

    for (const book of data) {
        const row = document.createElement('li');
        row.className = 'flex flex-wrap items-center gap-2 py-3';
        const name = document.createElement('span');
        name.className = 'mr-auto';
        name.textContent = book.title + ' — ' + book.author_name;
        row.append(name, action('Detalhes', () => showBook(book.id)));
        if (canWrite) row.append(action('Editar', () => openForm(book.id)));
        if (user.role === 'admin') row.append(action('Excluir', () => deleteBook(book)));
        list.append(row);
    }

    if (!data.length) {
        const empty = document.createElement('li');
        empty.textContent = 'Nenhum livro cadastrado.';
        list.append(empty);
    }
}

async function showBook(id) {
    const { data: book } = await api('/books/' + id);
    form.hidden = true;

    const title = document.createElement('h2');
    title.className = 'text-lg font-semibold';
    title.textContent = book.title;
    const image = document.createElement('img');
    image.className = 'h-28 object-contain';
    image.src = book.cover_url;
    image.alt = 'Capa de ' + book.title;
    const info = document.createElement('p');
    info.textContent = 'Autor: ' + book.author_name + ' | ISBN: ' + book.isbn
        + ' | Ano: ' + (book.published_year ?? 'não informado');
    details.replaceChildren(title, image, info, action('Fechar', () => { details.hidden = true; }));
    details.hidden = false;
    details.scrollIntoView({ block: 'nearest' });
}

async function openForm(id = null) {
    const { data: authors } = await api('/authors');
    const book = id === null ? null : (await api('/books/' + id)).data;

    editingId = id;
    details.hidden = true;
    form.reset();
    const author = form.elements.author_id;
    author.replaceChildren(new Option('Selecione', ''));
    for (const item of authors) author.add(new Option(item.name, item.id));

    if (book) {
        form.elements.title.value = book.title;
        form.elements.isbn.value = book.isbn;
        form.elements.published_year.value = book.published_year ?? '';
        author.value = book.author_id;
    }

    element('form-title').textContent = book ? 'Editar livro' : 'Cadastrar livro';
    form.hidden = false;
    form.elements.title.focus();
}

async function deleteBook(book) {
    if (!confirm('Excluir este livro?')) return;
    await api('/books/' + book.id, 'DELETE');
    form.hidden = true;
    details.hidden = true;
    await loadBooks();
    message('Livro excluído.');
}

login.addEventListener('submit', event => {
    event.preventDefault();
    run(async () => {
        const data = Object.fromEntries(new FormData(login));
        ({ token, user } = await api('/tokens', 'POST', data));

        login.reset();
        canWrite = ['admin', 'bibliotecario'].includes(user.role);
        element('account').textContent = user.name + ' — ' + user.role;
        element('new-book').hidden = !canWrite;
        login.hidden = true;
        element('library').hidden = false;
        await loadBooks();
    });
});

form.addEventListener('submit', event => {
    event.preventDefault();
    run(async () => {
        const data = Object.fromEntries(new FormData(form));
        data.author_id = Number(data.author_id);
        data.published_year = data.published_year === '' ? null : Number(data.published_year);

        if (editingId === null) {
            await api('/books', 'POST', data);
        } else {
            await api('/books/' + editingId, 'PUT', data);
        }

        form.hidden = true;
        await loadBooks();
        message('Livro salvo.');
    });
});

element('new-book').addEventListener('click', () => run(() => openForm()));
element('cancel').addEventListener('click', () => { form.hidden = true; });
element('logout').addEventListener('click', () => run(async () => {
    await api('/tokens/current', 'DELETE');
    closeSession();
    message('Sessão encerrada.');
}));
