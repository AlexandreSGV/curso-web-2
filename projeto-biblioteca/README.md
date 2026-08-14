# Projeto da biblioteca

Esta é a única aplicação Laravel acumulativa de Web 2. Ela começou demonstrando o fluxo **rota → controller → view Blade** e agora possui a entidade `Book`, uma factory e dados previsíveis para consultas com Eloquent.

Ainda não há relacionamentos, controllers CRUD, autenticação ou API. Esses recursos serão adicionados gradualmente.

## Preparação

Use o Ubuntu no WSL e execute dentro desta pasta:

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
npm run build
php artisan migrate
php artisan db:seed
```

Abra `.env` e troque apenas `SUA_SENHA_LOCAL` pela senha do usuário MySQL `biblioteca`. Não envie o `.env` ao GitHub.

Antes de executar comandos de banco, confirme que `DB_DATABASE` no `.env` aponta para o banco de desenvolvimento `biblioteca`. Execute `db:seed` somente depois das migrations.

## Execução

```bash
php artisan serve
```

Acesse [http://localhost:8000](http://localhost:8000). A página deve mostrar **Biblioteca Web 2** e explicar o fluxo da requisição.

Para encerrar o servidor, pressione `Ctrl+C`.

## Consulte os dados

Abra o Tinker:

```bash
php artisan tinker
```

No prompt interativo, experimente:

```php
use App\Models\Book;

Book::all(['id', 'title', 'isbn', 'published_year']);

Book::where('published_year', '>=', 2021)
    ->orderBy('published_year', 'desc')
    ->get(['id', 'title', 'published_year']);
```

Digite `exit` para sair.

## Arquivos principais neste estado

- `routes/web.php`: associa o endereço `/` ao controller;
- `app/Http/Controllers/InicioController.php`: prepara os dados da página;
- `resources/views/biblioteca/inicio.blade.php`: monta o HTML;
- `database/migrations/2026_08_13_000000_create_books_table.php`: registra a estrutura inicial de `books`;
- `app/Models/Book.php`: representa os livros para o Eloquent;
- `database/factories/BookFactory.php`: define valores padrão para gerar livros;
- `database/seeders/DatabaseSeeder.php`: cria seis livros previsíveis para a aula;
- `resources/css/app.css`: define a apresentação visual e é processado pelo Vite;
- `.env.example`: documenta a configuração local sem publicar credenciais.

Consulte a [apostila de factories, seeders e Eloquent](../unidade-1/03-factories-seeders-eloquent/) para acompanhar esta evolução. As etapas anteriores permanecem nas apostilas de [migrations e models](../unidade-1/02-migrations-models/) e de [ambiente, framework e MVC](../unidade-1/01-ambiente-framework-mvc/).
