# Exemplo: banco da biblioteca populado

Este exemplo complementa a [apostila de Seeders e Factories](../README.md). Ele cria dados fictícios compatíveis com os relacionamentos definidos na apostila de [Models](../../05-models/README.md):

- `Author` 1–N `Book`;
- `Book` 1–1 `BookDetail`;
- `Book` N–N `Category`, pela pivot `book_category`.

Na primeira execução, o resultado esperado é de 8 autores, 40 livros, 40 detalhes, 6 categorias e entre 40 e 120 associações na pivot.

## Pré-requisitos

Use um projeto Laravel 13 com a conexão de banco configurada e as migrations anteriores já aplicadas. A estrutura esperada é:

| Tabela | Campos relevantes |
|---|---|
| `authors` | `id`, `name`, timestamps |
| `books` | `id`, `title`, `isbn`, `published_year`, `author_id`, timestamps |
| `book_details` | `id`, `book_id` único, `pages`, `summary`, timestamps |
| `categories` | `id`, `name` único, timestamps |
| `book_category` | `book_id`, `category_id`, `featured`, `position`, timestamps |

Os Models devem ter os métodos `author()`, `books()`, `detail()`, `book()` e `categories()` apresentados na apostila de Models. Nos quatro Models, acrescente:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;
}
```

O fragmento mostra `Book`; repita a trait em `Author`, `BookDetail` e `Category`, preservando o restante de cada classe.

Para a pivot completa, os relacionamentos muitos para muitos precisam usar:

```php
->withPivot('featured', 'position')
->withTimestamps();
```

## Arquivos e destinos

| Arquivo do exemplo | Destino no projeto Laravel |
|---|---|
| [AuthorFactory.php](database/factories/AuthorFactory.php) | `database/factories/AuthorFactory.php` |
| [BookFactory.php](database/factories/BookFactory.php) | `database/factories/BookFactory.php` |
| [BookDetailFactory.php](database/factories/BookDetailFactory.php) | `database/factories/BookDetailFactory.php` |
| [CategoryFactory.php](database/factories/CategoryFactory.php) | `database/factories/CategoryFactory.php` |
| [LibrarySeeder.php](database/seeders/LibrarySeeder.php) | `database/seeders/LibrarySeeder.php` |
| [DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) | `database/seeders/DatabaseSeeder.php` |

Se algum desses arquivos já existir, compare e integre o conteúdo necessário. Não mantenha duas classes com o mesmo nome.

## Executando

Na pasta do projeto:

```bash
php artisan migrate:status
php artisan migrate
php artisan db:seed
```

Para executar somente este cenário:

```bash
php artisan db:seed --class=LibrarySeeder
```

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

$book = App\Models\Book::with(['author', 'detail', 'categories'])->first();

$book->author->name;
$book->detail?->pages;

$book->categories->map(fn ($category) => [
    'name' => $category->name,
    'featured' => (bool) $category->pivot->featured,
    'position' => $category->pivot->position,
]);
```

Após a população, o exemplo de lazy/eager loading da apostila de [Eloquent](../../06-orm-eloquent/exemplos/) e o CRUD da apostila de [Controllers](../../08-controllers/) terão dados suficientes para a demonstração.

## Se a pivot for simples

O `LibrarySeeder` principal usa a versão com colunas extras. Se sua migration de `book_category` possui apenas as duas chaves, substitua o bloco que monta `$pivotAttributes` e chama `attach()` por:

```php
$book->categories()->attach($selectedCategories->pluck('id'));
```

Nesse caso, remova também `withPivot()` e `withTimestamps()` dos relacionamentos. A migration, os Models e o Seeder devem representar a mesma versão.

## Nova execução

As categorias fixas são reaproveitadas com `firstOrCreate()`. Uma nova execução acrescenta outros 8 autores, 40 livros, 40 detalhes e novas associações. Esse comportamento é intencional para a demonstração; confira as quantidades antes de comparar resultados de desempenho.

