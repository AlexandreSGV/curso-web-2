# Exemplo: biblioteca com capas de livros

Esta pasta reúne todo o código criado ou editado nas aulas para executar a biblioteca com capas. Inclui Models, migrations, Factories, Seeders, Controllers, Policies, Views, rotas e a imagem padrão. O Laravel fornece os arquivos padrão do projeto. Veja a [apostila de Storage](../README.md) e a [aula 11](../../11-autenticacao-autorizacao/) para entender o código e as permissões.

## 1. Criar o projeto e copiar os arquivos

Em um terminal WSL/Ubuntu ou Linux, na pasta que contém o repositório `curso-web-2`, crie um projeto ao lado dele:

```bash
laravel new biblioteca-storage
```

Use Laravel 13, sem starter kit, Blade quando solicitado e SQLite. Copie os arquivos:

```bash
origem=curso-web-2/unidade-1/12-storage/exemplos
cp -R "$origem/app/." biblioteca-storage/app/
cp -R "$origem/database/." biblioteca-storage/database/
cp -R "$origem/resources/." biblioteca-storage/resources/
cp -R "$origem/routes/." biblioteca-storage/routes/
cp -R "$origem/public/." biblioteca-storage/public/
cd biblioteca-storage
```

Se já tem o projeto com a versão atual do exemplo da aula 11, pode copiar os arquivos para ele em vez de criar outro. Caso tenha personalizado os arquivos, integre as mudanças preservando suas alterações.

## 2. Preparar e iniciar

Na primeira execução em um banco novo, crie as tabelas e os dados de aula:

```bash
php artisan migrate --seed
```

Se já tem os dados da aula 11, rode apenas `php artisan migrate`. A nova migration acrescenta `cover` sem apagar registros ou mudar as migrations anteriores. Os livros existentes começam com a capa padrão.

Em seguida, crie o link e inicie a aplicação:

```bash
php artisan storage:link
php artisan serve
```

Se o link já existir e estiver correto, prossiga para iniciar a aplicação. Acesse [http://localhost:8000/login](http://localhost:8000/login) ou a porta indicada pelo terminal. O exemplo usa o disco `public` padrão. As Views mantêm o Tailwind via CDN da aula anterior.

| E-mail de aula | Senha | Papel |
|---|---|---|
| `admin@example.com` | `senha-aula-123` | Exclui livros e pode criar e editar capas |
| `bibliotecario@example.com` | `senha-aula-123` | Cadastra e edita livros e capas |
| `cliente@example.com` | `senha-aula-123` | Consulta os livros; a capa aparece na listagem e nos detalhes |

## 3. Onde acompanhar o código

| Arquivo | O que observar |
|---|---|
| [Migration de capa](database/migrations/2026_11_17_000001_add_cover_to_books_table.php) e [Book](app/Models/Book.php) | Campo `cover` de texto, opcional |
| [BookController](app/Http/Controllers/BookController.php) | Validação do arquivo, armazenamento, substituição e exclusão |
| [Formulário de cadastro](resources/views/books/create.blade.php) e [edição](resources/views/books/edit.blade.php) | Envio com `multipart/form-data` |
| [Campos do formulário](resources/views/books/_form.blade.php) | Campo de upload e opção de remover a capa atual |
| [Parcial de capa](resources/views/books/_cover.blade.php) e [imagem padrão](public/images/cover-default.svg) | Exibir a capa enviada ou a imagem padrão |

As capas enviadas ficam em `storage/app/public/covers` no projeto que está rodando. Se o arquivo está nessa pasta, mas a URL `/storage/covers/...` não abre, confira o link `public/storage` e se o servidor está servindo o diretório `public/` desse projeto.
