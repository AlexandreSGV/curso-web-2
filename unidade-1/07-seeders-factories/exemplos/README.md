# Exemplo: banco da biblioteca populado

Este exemplo complementa a [apostila de Seeders e Factories](../README.md). Ele cria dados fictícios compatíveis com os relacionamentos definidos na apostila de [Models](../../05-models/README.md):

- `Author` 1–N `Book`;
- `Book` 1–1 `BookDetail`;
- `Book` N–N `Category`, pela pivot `book_category`.

Com as tabelas da biblioteca inicialmente vazias, a primeira execução produz 8 autores, 40 livros, 40 detalhes, 6 categorias e entre 40 e 120 associações na pivot.

O [LibrarySeeder](database/seeders/LibrarySeeder.php) utiliza arrays e laços `foreach`. Ele cria as categorias, percorre os autores e seus livros e chama `attach()` para cada categoria sorteada. `shuffle()` e `array_slice()` selecionam de 1 a 3 categorias sem repetição no mesmo livro; a primeira recebe `featured = true`, e `position` registra a ordem a partir de 1.

## Pré-requisitos

Use um projeto Laravel 13 (PHP 8.3 ou superior), com a conexão de banco configurada no `.env` e as dependências instaladas por `composer install`, incluindo o Faker utilizado pelas Factories.

Esta pasta reúne os arquivos para montar o exemplo dentro desse projeto Laravel. A estrutura fornecida é:

| Tabela | Campos relevantes |
|---|---|
| `authors` | `id`, `name`, timestamps |
| `books` | `id`, `title`, `isbn` único, `published_year` opcional, `author_id` obrigatório, timestamps |
| `book_details` | `id`, `book_id` único, `pages`, `summary`, timestamps |
| `categories` | `id`, `name` único, timestamps |
| `book_category` | `book_id`, `category_id`, `featured`, `position`, timestamps |

Os quatro [Models fornecidos](app/Models/) já incluem `HasFactory`, `Fillable` e os relacionamentos nos dois sentidos. `Book::categories()` e `Category::books()` utilizam `withPivot('featured', 'position')->withTimestamps()`, de acordo com a pivot completa. O Model `Book` também reúne os mutators de título e ISBN e os métodos `publicationAge()` e `isClassic()` estudados anteriormente.

`book_category` é manipulada por `belongsToMany()` e não precisa de um Model próprio neste exemplo.

## Arquivos e destinos

| Arquivo do exemplo | Destino no projeto Laravel |
|---|---|
| [Author.php](app/Models/Author.php) | `app/Models/Author.php` |
| [Book.php](app/Models/Book.php) | `app/Models/Book.php` |
| [BookDetail.php](app/Models/BookDetail.php) | `app/Models/BookDetail.php` |
| [Category.php](app/Models/Category.php) | `app/Models/Category.php` |
| [As cinco migrations](database/migrations/) | `database/migrations/`, conforme a preparação abaixo |
| [AuthorFactory.php](database/factories/AuthorFactory.php) | `database/factories/AuthorFactory.php` |
| [BookFactory.php](database/factories/BookFactory.php) | `database/factories/BookFactory.php` |
| [BookDetailFactory.php](database/factories/BookDetailFactory.php) | `database/factories/BookDetailFactory.php` |
| [CategoryFactory.php](database/factories/CategoryFactory.php) | `database/factories/CategoryFactory.php` |
| [LibrarySeeder.php](database/seeders/LibrarySeeder.php) | `database/seeders/LibrarySeeder.php` |
| [DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) | `database/seeders/DatabaseSeeder.php` |

Se algum desses arquivos já existir, compare e integre o conteúdo necessário. Mantenha uma única classe por nome. No `DatabaseSeeder` existente, inclua a chamada a `LibrarySeeder::class` uma única vez, preservando as outras chamadas necessárias ao projeto.

## Migrations e ordem de execução

As migrations abaixo criam diretamente a estrutura final apresentada na apostila de Models. Por isso, a criação de `books` já inclui `author_id`, e a criação da pivot já inclui as colunas extras.

| Ordem | Arquivo | Dependências |
|---|---|---|
| 1 | [2026_09_11_000001_create_authors_table.php](database/migrations/2026_09_11_000001_create_authors_table.php) | Nenhuma |
| 2 | [2026_09_11_000002_create_books_table.php](database/migrations/2026_09_11_000002_create_books_table.php) | `authors` |
| 3 | [2026_09_11_000003_create_book_details_table.php](database/migrations/2026_09_11_000003_create_book_details_table.php) | `books` |
| 4 | [2026_09_11_000004_create_categories_table.php](database/migrations/2026_09_11_000004_create_categories_table.php) | Nenhuma |
| 5 | [2026_09_11_000005_create_book_category_table.php](database/migrations/2026_09_11_000005_create_book_category_table.php) | `books` e `categories` |

Preserve a ordem dos timestamps. O Laravel executa `up()` nessa sequência e, ao reverter esse conjunto, executa `down()` na ordem inversa.

As restrições garantem que cada livro tenha no máximo um registro de detalhes e que cada par livro/categoria apareça uma única vez. Excluir um livro remove seus detalhes e associações; excluir uma categoria remove suas associações. Um autor com livros não pode ser excluído.

## Preparando o projeto

### Projeto novo, sem as tabelas da biblioteca

1. Copie os quatro Models para `app/Models/`.
2. Copie as cinco migrations para `database/migrations/`. Confira se não há outra migration criando essas mesmas tabelas.
3. Copie as quatro Factories para `database/factories/` e os Seeders para `database/seeders/`.
4. Configure a conexão no `.env` e execute os comandos da próxima seção.

### Projeto que já acompanhou as apostilas anteriores

Mantenha as migrations já executadas ou compartilhadas. Se a estrutura já coincide com a tabela de pré-requisitos, basta integrar os Models, Factories e Seeders; não copie novamente as migrations de criação.

Se faltarem campos ou tabelas, use os arquivos como referência e crie novas migrations somente para as diferenças. Por exemplo, se `books` já existe, adicione `author_id` com `Schema::table()` em vez de executar outra `Schema::create('books', ...)`. Havendo livros sem autor, siga a transição com coluna inicialmente opcional descrita na [apostila de Models](../../05-models/README.md#7-relacionamento-um-para-muitos), associe os autores e só então torne a coluna obrigatória.

O `projeto-biblioteca` do repositório contém uma versão inicial de `books`, `BookFactory` e `DatabaseSeeder`. Confira esses arquivos ao integrar o exemplo. Mesmo com o banco vazio, duas migrations criando `books` causam erro; `migrate:fresh` também falha se ambas continuarem no projeto.

## Executando

Na pasta do projeto:

```bash
php artisan migrate:status
php artisan migrate
php artisan db:seed --class=LibrarySeeder
```

O comando acima executa apenas o cenário da biblioteca. Para utilizar a chamada registrada em `DatabaseSeeder`:

```bash
php artisan db:seed
```

Os dois comandos `db:seed` são alternativas. Se forem executados consecutivamente, o cenário será inserido duas vezes.

Em um banco local que possa ser totalmente descartado:

```bash
php artisan migrate:fresh --seed
```

**Atenção:** o último comando apaga todas as tabelas e dados da conexão configurada antes de reconstruí-los.

## Conferência no Tinker

```bash
php artisan tinker
```

```php
App\Models\Author::count();
App\Models\Book::count();
App\Models\BookDetail::count();
App\Models\Category::count();
Illuminate\Support\Facades\DB::table('book_category')->count();

$book = App\Models\Book::with(['author', 'detail', 'categories'])->first();

$book->author->name;
$book->detail?->pages;

$categoryData = [];

foreach ($book->categories as $category) {
    $categoryData[] = [
        'name' => $category->name,
        'featured' => (bool) $category->pivot->featured,
        'position' => $category->pivot->position,
    ];
}

$categoryData;

App\Models\Author::withCount('books')->get(['id', 'name']);
App\Models\Category::withCount('books')->get(['id', 'name']);
App\Models\BookDetail::with('book')->first()->book->title;
```

Após a população, o exemplo de lazy/eager loading da apostila de [Eloquent](../../06-orm-eloquent/exemplos/) e o CRUD da apostila de [Controllers](../../08-controllers/) terão dados suficientes para a demonstração.

## Se a pivot for simples

Os arquivos fornecidos já estão alinhados à pivot completa. Se optar por manter no seu projeto a versão anterior com apenas as duas chaves, mantenha o `foreach ($selectedCategories as $category)` e substitua apenas a chamada a `attach()` por:

```php
$book->categories()->attach($category->id);
```

Nesse caso, remova também `withPivot()` e `withTimestamps()` dos relacionamentos. A migration, os Models e o Seeder devem representar a mesma versão.

Na versão completa, se a associação falhar por coluna inexistente, confira as colunas da tabela real: `book_id`, `category_id`, `featured`, `position`, `created_at` e `updated_at`. Se faltar alguma, siga a preparação para projetos existentes e crie uma nova migration com a alteração necessária. Editar uma migration já executada não atualiza o banco.

## Nova execução

As categorias fixas são reaproveitadas com `firstOrCreate()`. Uma nova execução acrescenta outros 8 autores, 40 livros, 40 detalhes e novas associações. Esse comportamento é intencional para a demonstração; confira as quantidades antes de comparar resultados de desempenho.
