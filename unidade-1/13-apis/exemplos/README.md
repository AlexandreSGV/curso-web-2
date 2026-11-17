# Biblioteca: API Laravel e cliente Node.js

`laravel/` contém todos os arquivos da aplicação desenvolvidos nas aulas, incluindo a biblioteca com capas da aula 12. Copie-os para um projeto Laravel novo. `cliente-node/` é um app separado, com HTML, CSS, JavaScript e Tailwind via CDN. O servidor Node.js apenas entrega os arquivos; não precisa de `npm install`.

## 1. Preparar o Laravel

Na pasta que contém o repositório `curso-web-2`, crie o projeto ao lado:

```bash
laravel new biblioteca-api
cd biblioteca-api
php artisan install:api --without-migration-prompt
```

Use Laravel 13, sem starter kit, Blade quando solicitado e SQLite. O último comando prepara o Sanctum, registra `routes/api.php` no bootstrap e publica a migration de tokens. A opção adia a execução das migrations até depois da cópia. Se a migration `create_personal_access_tokens_table` não aparecer em `database/migrations/`, execute `php artisan vendor:publish --tag=sanctum-migrations` antes de migrar.

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

## 3. Conferir o contrato e experimentar requisições

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

O [arquivo OpenAPI](openapi.yaml) pode ser aberto no Swagger Editor para ler os campos e as respostas. Para testar com credenciais, use o cliente de aula, `curl` local ou uma Swagger UI servida por você. A listagem não usa paginação para manter a demonstração curta.
