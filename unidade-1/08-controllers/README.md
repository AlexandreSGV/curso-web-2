# Controllers no Laravel: coordenando requisições e respostas

Nas apostilas anteriores, usamos migrations para definir as tabelas, Models para representar livros, autores e categorias, e o Eloquent para trabalhar com os registros.

Agora vamos conectar essas operações às ações do usuário: abrir a lista de livros, enviar um cadastro, editar um registro ou solicitar sua exclusão. No Laravel, essa coordenação costuma ficar no **Controller**.

Os exemplos continuam o sistema de biblioteca e utilizam Laravel 13. O [exemplo completo](exemplos/) acompanha a apostila com Controller, rotas e telas simples.

## Índice

1. [O papel do Controller](#1-o-papel-do-controller)
2. [Criando um Controller e sua primeira rota](#2-criando-um-controller-e-sua-primeira-rota)
3. [Recebendo dados com Request](#3-recebendo-dados-com-request)
4. [Escolhendo a resposta](#4-escolhendo-a-resposta)
5. [As actions de um CRUD](#5-as-actions-de-um-crud)
6. [Recebendo um livro pelo endereço](#6-recebendo-um-livro-pelo-endereço)
7. [BookController: reunindo o CRUD](#7-bookcontroller-reunindo-o-crud)
8. [Ligando os formulários ao Controller](#8-ligando-os-formulários-ao-controller)
9. [Executando e conferindo o exemplo](#9-executando-e-conferindo-o-exemplo)
10. [Quadro de consulta rápida](#10-quadro-de-consulta-rápida)

## 1. O papel do Controller

Um **Controller**, ou controlador, é uma classe que agrupa métodos responsáveis por atender requisições. Um método público usado por uma rota é frequentemente chamado de **action**, isto é, uma ação.

Pense no Controller como o atendente de uma biblioteca: ele recebe o pedido, identifica o que precisa ser feito, solicita a operação apropriada e encaminha a resposta. Não precisa guardar o acervo no balcão nem realizar sozinho todas as tarefas da biblioteca.

| Parte da aplicação | Responsabilidade no exemplo |
|---|---|
| Rota | Relacionar método HTTP e endereço à action |
| Controller | Receber os dados, coordenar a operação e retornar a resposta |
| Model e Eloquent | Representar o livro e consultar ou persistir seus dados |
| View | Organizar o HTML apresentado ao usuário |
| Migration | Definir a estrutura das tabelas |

Ao acessar a lista de livros:

1. O navegador envia `GET /books`.
2. A rota direciona a requisição para `BookController::index`.
3. A action consulta os livros por meio de `Book` e do Eloquent.
4. O Controller fornece os dados à View; o Laravel renderiza o HTML e o devolve ao navegador.

O Controller executa **no servidor**, não no navegador. Ele não deve concentrar todo o sistema: cálculos e regras da entidade continuam no Model ou em classes de serviço; o HTML fica nas Views.

## 2. Criando um Controller e sua primeira rota

Na pasta do projeto Laravel, execute:

```bash
php artisan make:controller BookController
```

O arquivo será criado em `app/Http/Controllers/BookController.php`.

Usaremos o nome `BookController`: nome da entidade no singular, seguido de `Controller`, com iniciais maiúsculas. Esse padrão ajuda a localizar os responsáveis por livros, autores e categorias.

Um primeiro exemplo:

```php
<?php

namespace App\Http\Controllers;

class BookController extends Controller
{
    public function index(): string
    {
        return 'Lista de livros';
    }
}
```

Em `routes/web.php`:

```php
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/books', [BookController::class, 'index']);
```

A rota informa **qual classe e qual método** devem atender `GET /books`. Criar o arquivo do Controller, por si só, não cria uma URL. Veja a [documentação de Controllers](https://laravel.com/docs/13.x/controllers#basic-controllers).

`namespace` identifica onde a classe está organizada; `use` permite referenciar outras classes pelo nome curto; `extends Controller` indica a herança da classe base do projeto. `: string` informa o tipo de retorno.

> Se `BookController.php` já existe, acrescente ou adapte os métodos nele. Não crie outra classe com o mesmo nome nem sobrescreva as actions anteriores sem conferir seu conteúdo.

## 3. Recebendo dados com `Request`

O objeto `Request` reúne informações da requisição, como campos de formulário e parâmetros de consulta da URL.

```php
use Illuminate\Http\Request;

public function index(Request $request)
{
    $title = $request->input('title');
    // Utilizar o título como filtro da consulta.
}
```

Ao acessar `/books?title=Laravel`, `$title` recebe `Laravel`. O Laravel fornece o objeto automaticamente; não precisamos criar `new Request()` nem ler diretamente `$_GET` e `$_POST`.

| Operação | Exemplo | Uso |
|---|---|---|
| Ler uma entrada | `$request->input('title')` | Obter o título enviado |
| Verificar se foi preenchida | `$request->filled('title')` | Aplicar um filtro somente quando houver valor |
| Validar entradas | `$request->validate([...])` | Conferir os dados antes de utilizá-los |

Os nomes usados em `input()` correspondem aos nomes dos parâmetros ou aos atributos `name` dos campos do formulário. Consulte [Request: acesso às entradas](https://laravel.com/docs/13.x/requests#retrieving-input).

## 4. Escolhendo a resposta

Uma action deve devolver a resposta adequada à operação. Ela não precisa retornar sempre uma página HTML.

| Resposta | Exemplo |
|---|---|
| Texto simples | `return 'Operação concluída';` |
| View com dados | `return view('books.index', compact('books'));` |
| Redirecionamento | `return redirect()->route('books.index');` |
| Dados em JSON | `return response()->json(['title' => $book->title]);` |

`view('books.index', ...)` procura o arquivo `resources/views/books/index.blade.php`. `compact('books')` equivale a `['books' => $books]`: os dados ficam disponíveis na View como `$books`.

Um **redirecionamento** pede que o navegador faça outra requisição. Depois de cadastrar um livro, por exemplo, podemos direcioná-lo à página de detalhes. Isso evita reenviar o mesmo cadastro ao atualizar a página seguinte, embora não impeça sozinho envios duplicados.

Uma resposta JSON pode atender uma interface separada, como estudamos em MVC; não utilizaremos uma API neste exemplo. As opções estão em [respostas HTTP do Laravel](https://laravel.com/docs/13.x/responses).

## 5. As actions de um CRUD

O Laravel oferece um padrão chamado **resource controller**: um Controller com sete actions convencionais para organizar um CRUD.

Para gerar o esqueleto com o Model `Book`:

```bash
php artisan make:controller BookController --model=Book --resource
```

Esse comando é uma alternativa ao comando simples anterior, para quando o arquivo ainda não existe. Ele gera os métodos, mas não implementa as operações, as Views ou as migrations.

Em `routes/web.php`, substitua a rota isolada de `/books` por:

```php
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::resource('books', BookController::class);
```

Essa definição registra as seguintes rotas, seguindo as [convenções de resource controllers](https://laravel.com/docs/13.x/controllers#resource-controllers):

| Método HTTP | Endereço | Action | Responsabilidade | Nome da rota |
|---|---|---|---|---|
| GET | `/books` | `index` | Listar livros | `books.index` |
| GET | `/books/create` | `create` | Exibir formulário de cadastro | `books.create` |
| POST | `/books` | `store` | Salvar um novo livro | `books.store` |
| GET | `/books/{book}` | `show` | Exibir um livro | `books.show` |
| GET | `/books/{book}/edit` | `edit` | Exibir formulário de edição | `books.edit` |
| PUT/PATCH | `/books/{book}` | `update` | Salvar alterações | `books.update` |
| DELETE | `/books/{book}` | `destroy` | Excluir um livro | `books.destroy` |

**`create` não salva e `edit` não atualiza.** Essas actions exibem formulários; quem persiste os dados é `store` ou `update`.

Também não confunda `BookController::create()`, que mostra o formulário, com `Book::create([...])`, que insere um registro pelo Eloquent.

Os nomes das rotas permitem gerar endereços sem escrevê-los manualmente. `route('books.show', $book)` produz o endereço do livro correspondente, como `/books/3`.

Para conferir as rotas registradas:

```bash
php artisan route:list --path=books
```

## 6. Recebendo um livro pelo endereço

Na rota `/books/{book}`, o trecho entre chaves representa um valor variável. Ao acessar `/books/3`, queremos o livro de ID `3`.

Podemos receber o próprio Model na action:

```php
public function show(Book $book): View
{
    return view('books.show', compact('book'));
}
```

Essa funcionalidade é chamada de **route model binding**: o Laravel usa o parâmetro para buscar o registro e entregar o objeto à action. Com as convenções deste projeto, a busca utiliza a coluna `id`; se não encontrar, a resposta é 404.

Observe a correspondência entre `{book}` e `$book`, e o tipo `Book`. Em vez de repetir `Book::findOrFail($id)`, recebemos o livro já encontrado. O exemplo completo abaixo inclui os imports necessários. Consulte [vinculação implícita de Models](https://laravel.com/docs/13.x/routing#implicit-binding).

Encontrar um registro não significa que o usuário tenha permissão para modificá-lo. A autorização é uma verificação diferente.

## 7. `BookController`: reunindo o CRUD

Usaremos os mesmos campos: `title`, `isbn`, `published_year` e `author_id`. As relações `author()` e `categories()` continuam nos Models. A listagem mantém o filtro, a paginação e o eager loading da apostila anterior.

Este código está disponível em [exemplos/BookController.php](exemplos/BookController.php). Ele substitui o primeiro exemplo de texto simples, não deve ser acrescentado como uma segunda classe.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['title' => ['nullable', 'string', 'max:255']]);

        $query = Book::with(['author', 'categories'])->orderBy('title');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        $books = $query->paginate(10)->withQueryString();

        return view('books.index', compact('books'));
    }

    public function create(): View
    {
        $authors = Author::orderBy('name')->get();

        return view('books.create', compact('authors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
            'published_year' => ['nullable', 'integer', 'between:1000,2100'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
        ]);

        $book = Book::create($validated);

        return redirect()->route('books.show', $book);
    }

    public function show(Book $book): View
    {
        $book->load(['author', 'categories']);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $authors = Author::orderBy('name')->get();

        return view('books.edit', compact('book', 'authors'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($book),
            ],
            'published_year' => ['nullable', 'integer', 'between:1000,2100'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
        ]);

        $book->update($validated);

        return redirect()->route('books.show', $book);
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('books.index');
    }
}
```

### O que observar nesse código

- **`index`:** consulta os livros e suas relações; `withQueryString()` mantém o filtro ao mudar de página.
- **`create` e `edit`:** buscam autores para o campo de seleção. Na edição, a View também recebe o livro atual.
- **`store` e `update`:** validam, persistem e redirecionam. O `Fillable` de `Book` deve permitir os quatro campos, mas não substitui a validação.
- **`show`:** usa `load()` para carregar relações de um objeto que já foi recuperado. `with()` prepara esse carregamento durante a consulta inicial.
- **`destroy`:** remove o livro. Os efeitos sobre detalhes e associações dependem das chaves estrangeiras das migrations; não estamos apagando o autor ou as categorias.

`View` e `RedirectResponse` indicam os tipos de resposta das actions.

### Validar um cadastro é diferente de validar uma edição

`unique:books,isbn` rejeita um ISBN já cadastrado. Na edição, `Rule::unique('books', 'isbn')->ignore($book)` permite manter o ISBN do próprio livro, mas continua impedindo o uso do ISBN de outro livro. Passe o Model recuperado à regra, não um identificador fornecido livremente no formulário.

`exists:authors,id` verifica se o autor escolhido existe. O intervalo de anos e o tamanho do ISBN mantêm a simplificação didática anterior; não validamos o dígito verificador do ISBN.

Nos formulários Web, uma falha em `validate()` interrompe a action e normalmente redireciona de volta com erros e dados anteriores. O Eloquent só recebe `$validated`, não todos os dados de `$request->all()`. Veja [validação no Laravel](https://laravel.com/docs/13.x/validation).

Para este primeiro contato, deixamos a validação no Controller. Conforme o código crescer, regras de entrada podem ir para um **Form Request**; regras de negócio complexas devem permanecer no Model ou em serviços.

## 8. Ligando os formulários ao Controller

O Controller precisa receber uma requisição com o endereço, o método HTTP e os campos esperados. Um formulário de cadastro em Blade pode ser:

```blade
<form method="POST" action="{{ route('books.store') }}">
    @csrf

    <label>Título</label>
    <input name="title" value="{{ old('title') }}" required>

    <label>ISBN</label>
    <input name="isbn" value="{{ old('isbn') }}" maxlength="13" required>

    <label>Ano</label>
    <input name="published_year" type="number" value="{{ old('published_year') }}">

    <label>Autor</label>
    <select name="author_id" required>
        <option value="">Selecione</option>
        @foreach ($authors as $author)
            <option value="{{ $author->id }}" @selected(old('author_id') == $author->id)>
                {{ $author->name }}
            </option>
        @endforeach
    </select>

    <button type="submit">Cadastrar</button>
</form>
```

`@csrf` inclui um token de proteção contra requisições forjadas. `old()` recupera o valor enviado quando uma validação falha. Os erros ficam disponíveis em `$errors`; o [formulário do exemplo](exemplos/views/books/_form.blade.php) já os exibe.

Para editar, o formulário usa `POST` com `@method('PUT')`:

```blade
<form method="POST" action="{{ route('books.update', $book) }}">
    @csrf
    @method('PUT')
    {{-- Aqui entram os mesmos campos, preenchidos com os valores do livro. --}}
</form>
```

Para excluir:

```blade
<form method="POST" action="{{ route('books.destroy', $book) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Excluir livro</button>
</form>
```

Formulários HTML não enviam `PUT` ou `DELETE` diretamente. `@method` informa ao Laravel qual operação interpretar. Não use um link `GET` para excluir. Consulte [formulários no Blade](https://laravel.com/docs/13.x/blade#forms).

## 9. Executando e conferindo o exemplo

A pasta [exemplos](exemplos/) contém:

- [BookController.php](exemplos/BookController.php), com as sete actions;
- [web.php](exemplos/web.php), com a definição das rotas;
- [Views de livros](exemplos/views/books/), com listagem, detalhes e formulários mínimos;
- [instruções de preparação e conferência](exemplos/README.md).

Antes de copiar, confirme os Models, campos e relacionamentos das apostilas de [Models](../05-models/README.md) e [Eloquent](../06-orm-eloquent/README.md). O projeto-base do repositório ainda representa uma etapa anterior: o exemplo depende dessa preparação.

O formulário altera somente os quatro campos de `Book`. As categorias já associadas são exibidas e preservadas; sua manutenção não é parte deste exemplo.

A próxima apostila, [Views e Blade](../09-views/), amplia a interface com layout compartilhado, CRUDs de autores e categorias e manutenção dos detalhes e das associações de cada livro.

Se já adicionou as actions `index_lazy_loading` e `index_eager_loading`, mantenha-as ao integrar o Controller. Suas rotas específicas devem ficar **antes** de `Route::resource(...)` para não serem confundidas com `/books/{book}`.

Na pasta do projeto, execute `php artisan serve` e acesse `/books`. Siga um cadastro até a página de detalhes; depois edite e exclua um livro de teste. Ao editar, experimente manter o mesmo ISBN e observe por que `ignore($book)` é necessário.

Este CRUD é para estudo local: não possui login ou autorização. Antes de expô-lo a outros usuários, essas verificações precisam ser implementadas; validação e proteção CSRF não substituem controle de acesso.

## 10. Quadro de consulta rápida

| Elemento | Lembrete |
|---|---|
| Controller | Coordena uma requisição e sua resposta |
| Action | Método do Controller atendido por uma rota |
| `routes/web.php` | Define as rotas da interface Web |
| `Request` | Oferece acesso aos dados recebidos |
| `validate()` | Confere entradas e devolve os campos validados |
| `view()` | Prepara a View com seus dados |
| `redirect()->route()` | Direciona o navegador a uma rota nomeada |
| `Route::resource()` | Registra as rotas convencionais do CRUD |
| `Book $book` | Recebe um Model resolvido pelo parâmetro da rota |
| `@csrf` | Inclui proteção nos formulários |
| `@method()` | Permite representar PUT, PATCH ou DELETE em um formulário POST |

## O que você precisa guardar

1. A rota escolhe a action; o Controller coordena a operação e retorna a resposta.
2. O Controller executa no servidor e não deve concentrar HTML ou todas as regras do sistema.
3. `Request` permite ler e validar os dados recebidos.
4. `create` e `edit` exibem formulários; `store` e `update` salvam dados.
5. `Route::resource()` padroniza as rotas, mas não implementa o CRUD sozinho.
6. `Book $book` pode substituir a busca manual pelo identificador; a ausência do registro gera 404.
7. Valide antes de persistir e permita que o livro mantenha o próprio ISBN ao editar.
8. Após gravar, redirecione; para formulários de alteração, use CSRF e o método HTTP apropriado.

## Referências

- [Laravel 13 - Controllers](https://laravel.com/docs/13.x/controllers)
- [Laravel 13 - Rotas e route model binding](https://laravel.com/docs/13.x/routing)
- [Laravel 13 - Requisições](https://laravel.com/docs/13.x/requests)
- [Laravel 13 - Respostas](https://laravel.com/docs/13.x/responses)
- [Laravel 13 - Validação](https://laravel.com/docs/13.x/validation)
- [Laravel 13 - Blade](https://laravel.com/docs/13.x/blade)
- Slides atuais de Web 2: MVC (página 68) e Controller com paginação (página 112 do PDF consolidado).
