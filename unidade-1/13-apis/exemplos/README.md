# Biblioteca: API Laravel e cliente Node.js

`laravel/` contém todos os arquivos da aplicação desenvolvidos nas aulas, incluindo a biblioteca com capas da aula 12. Copie-os para um projeto Laravel novo. `cliente-node/` é um app separado, com HTML, CSS, JavaScript e Tailwind via CDN. O servidor Node.js apenas entrega os arquivos; não precisa de `npm install`.

## 1. Preparar o Laravel

Na pasta que contém o repositório `curso-web-2`, crie um projeto Laravel 13 ao lado. O Composer já faz parte do [ambiente da disciplina](../../../apoio/ambiente-web2-wsl/).

```bash
composer create-project laravel/laravel biblioteca-api "13.*"
```

Se estiver usando o instalador do Laravel, execute `laravel new biblioteca-api` e escolha um projeto sem starter kit, Blade quando solicitado e SQLite.

Entre na pasta do projeto e prepare a API.

```bash
cd biblioteca-api
php artisan install:api --without-migration-prompt
```

O comando prepara o Sanctum, registra `routes/api.php` no bootstrap e publica a migration de tokens. A opção adia a execução das migrations até depois da cópia. Se a migration `create_personal_access_tokens_table` não aparecer em `database/migrations/`, execute `php artisan vendor:publish --tag=sanctum-migrations` antes de migrar.

O projeto novo usa SQLite por padrão. Se você usar MySQL, crie um banco vazio e ajuste `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD` no `.env` antes de rodar as migrations.

No terminal Linux ou WSL, ainda na pasta `biblioteca-api`:

```bash
origem=../curso-web-2/unidade-1/13-apis/exemplos/laravel
cp -R "$origem/app/." app/
cp -R "$origem/database/." database/
cp -R "$origem/resources/." resources/
cp -R "$origem/routes/." routes/
cp -R "$origem/public/." public/
php artisan migrate --seed
php artisan storage:link
php artisan route:list --path=api
php artisan serve --host=127.0.0.1 --port=8000
```

A cópia substitui as rotas de exemplo do instalador. O Model `User` incluído é o padrão com `HasApiTokens` acrescentado. Mantenha os arquivos que o instalador gera para a infraestrutura. O Seeder cria autores, livros e contas de demonstração.

Se continuar a partir do projeto já preparado na aula 12, rode `install:api` **antes** da cópia (uma vez), use apenas `php artisan migrate` para manter os dados, e aproveite o link de Storage existente. Se personalizou o Model `User` ou as rotas, integre as mudanças aos seus arquivos.

## 2. Iniciar o cliente

Com o Laravel rodando, abra outro terminal. A partir da pasta que contém `curso-web-2`:

```bash
cd curso-web-2/unidade-1/13-apis/exemplos/cliente-node
node server.mjs
```

Use Node.js 22 ou superior e abra [http://127.0.0.1:3000](http://127.0.0.1:3000). A URL da API está em `public/app.js`: `http://127.0.0.1:8000/api`. O acesso à internet carrega o Tailwind; o CRUD não depende dele.

| Conta local | Senha de aula | Acesso ao CRUD de livros |
|---|---|---|
| `admin@example.com` | `senha-aula-123` | Consultar, cadastrar, editar e excluir |
| `bibliotecario@example.com` | `senha-aula-123` | Consultar, cadastrar e editar |
| `cliente@example.com` | `senha-aula-123` | Consultar |

As telas Blade continuam disponíveis em [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login). Cadastre ou troque arquivos de capa por lá. Os livros consultados pelo cliente Node mostram as capas cadastradas ou a imagem padrão.

Veja como o servidor Node.js entrega a página e onde o JavaScript faz as chamadas à API. [README do cliente Node.js](cliente-node/README.md)

## 3. Como usar o arquivo OpenAPI

Imagine que você vai montar a tela de livros e precisa responder a três perguntas: qual endereço consultar, como enviar o token e onde encontrar a lista na resposta? O [`openapi.yaml`](openapi.yaml) reúne essas informações. Ele é um arquivo de texto escrito em **YAML** e segue o padrão **OpenAPI**, que descreve como um programa pode conversar com uma API.

Na biblioteca, esse arquivo é o **contrato entre o Laravel e o cliente**. As [rotas do Laravel](laravel/routes/api.php) encaminham as requisições aos controllers. O [JavaScript da tela](cliente-node/public/app.js) faz as chamadas. O YAML documenta o que os dois combinaram: não é preciso executá-lo nem copiá-lo para o projeto Laravel. Se a API mudar, atualize o contrato para que ele continue servindo de referência.

**Para consultar o contrato, mesmo com o Laravel desligado:**

1. Abra o [arquivo da aula](openapi.yaml) no GitHub e copie seu conteúdo. Abra o [Swagger Editor](https://editor.swagger.io/) e substitua o texto de exemplo pelo YAML copiado.
2. Na documentação exibida, expanda **`GET /books`**. O caminho é somado ao endereço em `servers` (`http://127.0.0.1:8000/api`). Portanto, a tela deve consultar **`GET http://127.0.0.1:8000/api/books`**.
3. Observe a autenticação e as respostas. A operação exige um token **Bearer**. Na resposta **200**, `data` contém a lista de livros; cada item tem campos como `title` e `author_name`. A resposta **401** indica que a API não recebeu um token válido.

## 4. Comparar o contrato com a API em execução

| Método e caminho | O que faz |
|---|---|
| `POST /api/tokens` | Recebe e-mail e senha e devolve um token |
| `GET /api/authors` | Lista autores para o formulário |
| `GET /api/books` | Lista os livros |
| `GET /api/books/{book}` | Consulta um livro |
| `POST /api/books` | Cadastra um livro |
| `PUT /api/books/{book}` | Atualiza um livro |
| `DELETE /api/books/{book}` | Exclui um livro e sua capa, se houver |
| `DELETE /api/tokens/current` | Revoga o token usado na requisição |

Todas as rotas, exceto a emissão de token, exigem `Authorization: Bearer …`. Envie `Accept: application/json`; quando enviar JSON no corpo, use também `Content-Type: application/json`.

Exemplo em um terminal Linux ou WSL, usando apenas a conta de aula:

```bash
curl -i http://127.0.0.1:8000/api/tokens \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"email":"cliente@example.com","password":"senha-aula-123"}'
```

Copie o valor de `token` da resposta. Substitua `COLE_O_TOKEN` pela credencial recebida:

```bash
curl -i http://127.0.0.1:8000/api/books \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer COLE_O_TOKEN'
```

Compare a resposta do segundo comando com a que você leu no Swagger Editor: o status deve ser **200** e a lista deve vir dentro de `data`. Agora repita a consulta sem o token:

```bash
curl -i -H 'Accept: application/json' http://127.0.0.1:8000/api/books
```

O status esperado é **401**. A URL é a mesma; o que mudou foi a identificação de quem fez o pedido.

## 5. Testar a API com o Postman Web

### Preparar o acesso local

1. Inicie o Laravel na porta `8000` seguindo a [preparação do Laravel](#1-preparar-o-laravel). O endereço base da API é `http://127.0.0.1:8000/api`.
2. Abra o Postman no navegador, entre na sua conta e abra seu espaço de trabalho. [Postman Web](https://go.postman.co/)
3. Instale e abra o **Postman Desktop Agent** no computador. Abra o seletor de agentes no rodapé do Postman Web, desative **Auto-select** se estiver ativo e escolha **Desktop Agent**. Ele envia as requisições a partir da sua máquina e alcança a API local. [Instalação e seleção do agente](https://learning.postman.com/docs/getting-started/basics/about-postman-agent/)

### Obter o token

1. No espaço de trabalho, clique em **+** e escolha **HTTP** para criar a requisição. Selecione **POST** e informe `http://127.0.0.1:8000/api/tokens`. [Criar uma requisição no Postman](https://learning.postman.com/latest-v-12/docs/use/send-requests/create-requests/request-basics#create-a-new-request)
2. Na aba **Headers**, adicione a chave `Accept` com o valor `application/json`.
3. Na aba **Body**, selecione **raw** e escolha **JSON** na lista de formatos. Cole os dados da conta de aula.

   ```json
   {
     "email": "admin@example.com",
     "password": "senha-aula-123"
   }
   ```

4. Clique em **Send**. Confira o status **201 Created** e copie o valor inteiro do campo `token`, sem as aspas.

### Consultar os livros com o token

1. Crie uma requisição **GET** para `http://127.0.0.1:8000/api/books`.
2. Na aba **Authorization**, escolha **Bearer Token** em **Auth Type** e cole o valor recebido no campo **Token**. O Postman monta o cabeçalho `Authorization` com esse valor. [Bearer Token no Postman](https://learning.postman.com/docs/use/send-requests/authorization/authorization-types/#bearer-token)
3. Adicione `Accept: application/json` em **Headers** e clique em **Send**. A resposta **200 OK** traz os livros em `data`.

### Cadastrar, editar e excluir um livro de teste

Repita a configuração de **Headers** e **Authorization** nas próximas requisições.

1. Envie **GET** para `http://127.0.0.1:8000/api/authors` e anote o `id` de um autor da resposta.
2. Envie **POST** para `http://127.0.0.1:8000/api/books`. Em **Body**, use **raw** e **JSON**. Ajuste `author_id` para o autor que você consultou e use um ISBN de 13 dígitos ainda livre.

   ```json
   {
     "title": "Livro de teste do Postman",
     "isbn": "9780000000025",
     "published_year": 2024,
     "author_id": 1
   }
   ```

3. Confira o status **201 Created** e anote o `data.id` do livro de teste.
4. Envie **PUT** para `http://127.0.0.1:8000/api/books/{id}`, trocando `{id}` pelo número anotado. Use os mesmos campos do JSON e altere o título. A resposta **200 OK** deve mostrar o novo título.
5. Use a mesma URL e envie **DELETE** com o token e sem corpo. Confira o status **204 No Content**.

### Revogar o token

Envie **DELETE** para `http://127.0.0.1:8000/api/tokens/current` com o mesmo Bearer Token e confira o status **204 No Content**. Repita a listagem de livros com esse token para conferir o status **401 Unauthorized**.
