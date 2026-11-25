# Cliente Node.js da biblioteca

O servidor Node.js entrega a página da biblioteca na porta `3000`. Depois de abrir a página, o JavaScript roda no navegador e usa `fetch()` para conversar diretamente com a API Laravel na porta `8000`.

## Executar

1. Prepare e inicie o Laravel na porta `8000`. [Guia de execução da API](../README.md#1-preparar-o-laravel)
2. Em outro terminal, dentro desta pasta e com Node.js 22 ou superior, execute o servidor.

   ```bash
   node server.mjs
   ```

3. Abra o cliente no navegador. [Biblioteca na porta 3000](http://127.0.0.1:3000/)
4. Use `admin@example.com` e a senha de aula `senha-aula-123` para experimentar a consulta, o cadastro, a edição e a exclusão. [Contas e permissões da aula](../README.md#2-iniciar-o-cliente)

## Arquivos da aplicação

| Arquivo | O que faz |
|---|---|
| [server.mjs](server.mjs) | Abre o servidor HTTP e entrega os arquivos da pasta `public`. |
| [public/index.html](public/index.html) | Define a página, os formulários e as áreas onde aparecem os livros e as mensagens. |
| [public/app.js](public/app.js) | Escuta os eventos da página, chama a API e monta a interface com os dados da resposta. A constante `API` guarda a URL do Laravel. |
| [public/style.css](public/style.css) | Estiliza os formulários e os avisos junto com o Tailwind carregado no HTML. |

## Da tela à API

1. Ao clicar em **Entrar**, o navegador envia o e-mail e a senha para `POST /api/tokens` e guarda o token recebido em memória.
2. A função `api()` inclui o token no cabeçalho `Authorization` e recebe as respostas da API. `loadBooks()` usa a lista em `data` para montar as linhas da página.
3. O formulário usa `POST /api/books` para cadastrar e `PUT /api/books/{id}` para editar. Os botões disponíveis acompanham o papel do usuário, e o Laravel verifica a permissão a cada requisição.
4. Ao clicar em **Sair**, a página pede ao Laravel que revogue o token atual e volta ao formulário de login.
