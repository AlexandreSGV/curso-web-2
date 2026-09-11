# Seeders e Factories no Laravel: populando o banco de dados

Nas apostilas anteriores, criamos a estrutura do banco com migrations, representamos livros, autores, detalhes e categorias com Models e usamos o Eloquent para consultar e alterar registros.

Para demonstrar essas operações, precisamos de dados. Cadastrar dezenas de livros manualmente seria demorado e produziria exemplos pouco variados. No Laravel, **Factories** descrevem como gerar registros válidos e **Seeders** coordenam a inserção desses registros no banco.

Esta apostila continua o sistema de biblioteca e utiliza Laravel 13. O [exemplo completo](exemplos/) popula os relacionamentos 1–1, 1–N e N–N estudados na apostila de [Models](../05-models/README.md).

A pasta de exemplos também inclui as [cinco migrations](exemplos/database/migrations/) e os [quatro Models](exemplos/app/Models/) compatíveis com as Factories e o `LibrarySeeder`. Consulte o [guia de preparação](exemplos/README.md#preparando-o-projeto) para montar o exemplo ou integrá-lo a um projeto já iniciado.

## Índice

1. [Por que popular o banco?](#1-por-que-popular-o-banco)
2. [Migration, Factory e Seeder](#2-migration-factory-e-seeder)
3. [Preparando os Models](#3-preparando-os-models)
4. [Criando Factories](#4-criando-factories)
5. [Gerando registros com Factories](#5-gerando-registros-com-factories)
6. [Criando um Seeder](#6-criando-um-seeder)
7. [Populando os relacionamentos da biblioteca](#7-populando-os-relacionamentos-da-biblioteca)
8. [Executando os Seeders](#8-executando-os-seeders)
9. [Conferindo os dados com o Eloquent](#9-conferindo-os-dados-com-o-eloquent)
10. [Cuidados e erros frequentes](#10-cuidados-e-erros-frequentes)
11. [Quadro de consulta rápida](#11-quadro-de-consulta-rápida)

## 1. Por que popular o banco?

Durante o desenvolvimento, dados de exemplo permitem:

- conferir telas, filtros e paginação;
- testar consultas e relacionamentos;
- comparar lazy loading e eager loading com vários registros;
- preparar demonstrações reproduzíveis;
- criar cenários para testes automatizados.

Esses dados são úteis em ambientes de desenvolvimento e teste. Eles não devem ser confundidos com dados reais fornecidos pelos usuários.

Podemos usar a seguinte analogia:

- a **Factory** é uma receita: informa como produzir um exemplo válido de cada entidade;
- o **Seeder** é o planejamento da produção: escolhe quais receitas executar, em que quantidade e como relacionar os resultados.

## 2. Migration, Factory e Seeder

Cada ferramenta possui uma responsabilidade diferente.

| Recurso | Responsabilidade | Exemplo na biblioteca |
|---|---|---|
| Migration | Definir ou modificar a estrutura | Criar `books` e a chave `author_id` |
| Model | Representar a entidade e seus relacionamentos | `Book`, `author()` e `categories()` |
| Factory | Definir valores de exemplo para um Model | Gerar título, ISBN e ano de um livro |
| Seeder | Coordenar a inserção de um conjunto de dados | Criar autores, livros, detalhes e categorias |

Uma Factory ou um Seeder **não cria colunas nem tabelas**. As migrations precisam ter sido executadas antes da inserção dos registros.

Os arquivos ficam normalmente em:

```text
database/factories
database/seeders
```

## 3. Preparando os Models

Para usar `Book::factory()`, o Model deve utilizar a trait `HasFactory`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Relacionamentos e demais comportamentos do Model.
}
```

Faça o mesmo em `Author`, `BookDetail` e `Category`. A trait acrescenta ao Model o método estático `factory()`; ela não substitui os relacionamentos nem as configurações já existentes.

O exemplo pressupõe a estrutura final da apostila de Models:

| Relação | Estrutura esperada |
|---|---|
| 1–1 | `book_details.book_id` único; `Book::detail()` e `BookDetail::book()` |
| 1–N | `books.author_id`; `Author::books()` e `Book::author()` |
| N–N | pivot `book_category`; `Book::categories()` e `Category::books()` |

A pivot utilizada no exemplo possui `featured`, `position`, `created_at` e `updated_at`. Portanto, os dois métodos `belongsToMany()` devem usar:

```php
return $this->belongsToMany(Category::class)
    ->withPivot('featured', 'position')
    ->withTimestamps();
```

No Model `Category`, a configuração equivalente utiliza `Book::class`.

## 4. Criando Factories

Os comandos abaixo criam uma Factory para cada Model:

```bash
php artisan make:factory AuthorFactory --model=Author
php artisan make:factory BookFactory --model=Book
php artisan make:factory BookDetailFactory --model=BookDetail
php artisan make:factory CategoryFactory --model=Category
```

Cada Factory possui um método `definition()`, que devolve os valores padrão de um registro.

### `AuthorFactory`

```php
<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Author> */
class AuthorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
```

### `BookFactory`

```php
<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Book> */
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->words(
                fake()->numberBetween(2, 5),
                true
            )),
            'isbn' => fake()->unique()->numerify('#############'),
            'published_year' => fake()
                ->optional(0.85)
                ->numberBetween(1850, now()->year),
            'author_id' => Author::factory(),
        ];
    }
}
```

O helper `fake()` oferece dados fictícios por meio da biblioteca Faker. Neste exemplo:

- `words()` forma um título;
- `numerify()` troca cada `#` por um algarismo, produzindo 13 dígitos;
- `unique()` evita repetir o valor durante essa execução;
- `optional(0.85)` informa que aproximadamente 85% dos livros receberão um ano;
- `Author::factory()` permite criar um autor quando nenhum for informado.

As outras Factories completas estão na pasta [exemplos/database/factories](exemplos/database/factories/).

> Os valores são fictícios e podem não corresponder a títulos ou ISBNs reais. Servem para desenvolvimento, demonstrações e testes.

## 5. Gerando registros com Factories

Uma Factory pode apenas montar um objeto ou também persistir o registro.

```php
// Cria o objeto em memória, sem inserir no banco.
$book = Book::factory()->make();

// Insere um registro no banco.
$book = Book::factory()->create();

// Insere dez registros.
$books = Book::factory()->count(10)->create();
```

Podemos substituir valores definidos pela Factory:

```php
$book = Book::factory()->create([
    'title' => 'Laravel para iniciantes',
    'published_year' => 2026,
]);
```

| Método | Resultado |
|---|---|
| `make()` | Model não persistido |
| `create()` | Model persistido |
| `count(10)` | Repete a criação dez vezes |
| `create([...])` | Substitui valores padrão e persiste |

A Factory descreve um registro isolado. Para montar um cenário completo e respeitar a ordem dos relacionamentos, utilizaremos um Seeder.

## 6. Criando um Seeder

Crie o Seeder da biblioteca:

```bash
php artisan make:seeder LibrarySeeder
```

O arquivo será criado em:

```text
database/seeders/LibrarySeeder.php
```

Todo Seeder possui um método `run()`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        // Inserções coordenadas pelo Seeder.
    }
}
```

Use o Seeder para organizar **quantidades, ordem e relações**. Dados estáveis, como os nomes das categorias da demonstração, podem ser informados diretamente. Dados numerosos, como autores e livros, podem ser gerados pelas Factories.

## 7. Populando os relacionamentos da biblioteca

O [LibrarySeeder completo](exemplos/database/seeders/LibrarySeeder.php) cria:

- 6 categorias conhecidas;
- 8 autores;
- 5 livros para cada autor, totalizando 40 livros;
- 1 detalhe para cada livro;
- entre 1 e 3 categorias para cada livro.

```php
<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookDetail;
use App\Models\Category;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Biografia',
            'Ciência',
            'Fantasia',
            'História',
            'Romance',
            'Tecnologia',
        ])->map(
            fn (string $name): Category =>
                Category::firstOrCreate(['name' => $name])
        );

        Author::factory()
            ->count(8)
            ->create()
            ->each(function (Author $author) use ($categories): void {
                Book::factory()
                    ->count(5)
                    ->for($author, 'author')
                    ->has(BookDetail::factory(), 'detail')
                    ->create()
                    ->each(function (Book $book) use ($categories): void {
                        $selectedCategories = $categories
                            ->random(random_int(1, 3))
                            ->values();

                        $pivotAttributes = $selectedCategories
                            ->mapWithKeys(
                                fn (Category $category, int $index): array => [
                                    $category->id => [
                                        'featured' => $index === 0,
                                        'position' => $index + 1,
                                    ],
                                ]
                            )
                            ->all();

                        $book->categories()->attach($pivotAttributes);
                    });
            });
    }
}
```

### Relação 1–N: autor e livros

```php
->for($author, 'author')
```

Cada livro criado recebe em `author_id` o identificador do autor atual. Como cada um dos 8 autores recebe 5 livros, teremos 40 registros válidos no lado “muitos”.

### Relação 1–1: livro e detalhes

```php
->has(BookDetail::factory(), 'detail')
```

Para cada livro, o Laravel usa o método `detail()` e cria um `BookDetail` relacionado. A restrição `unique()` de `book_details.book_id` continua sendo a garantia de que o livro não terá dois registros de detalhes.

### Relação N–N: livros e categorias

```php
$book->categories()->attach($pivotAttributes);
```

`attach()` insere as associações em `book_category`. A chave do array é o ID da categoria; os demais valores preenchem `featured` e `position`. A primeira categoria sorteada fica em destaque. Como o relacionamento usa `withTimestamps()`, o Eloquent também preenche os timestamps da pivot.

Se sua pivot for a versão simples, sem colunas extras e sem timestamps, substitua a montagem de `$pivotAttributes` por:

```php
$book->categories()->attach($selectedCategories->pluck('id'));
```

Não misture o código da pivot completa com a migration da pivot simples.

## 8. Executando os Seeders

O `DatabaseSeeder` é o ponto de entrada padrão. Use `call()` para executar o Seeder da biblioteca:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LibrarySeeder::class,
        ]);
    }
}
```

Primeiro, aplique as migrations pendentes. Depois, execute os Seeders:

```bash
php artisan migrate
php artisan db:seed
```

Para executar somente o Seeder da biblioteca:

```bash
php artisan db:seed --class=LibrarySeeder
```

Em um banco **local e descartável**, também é possível reconstruir toda a estrutura e popular os dados:

```bash
php artisan migrate:fresh --seed
```

> `migrate:fresh` apaga todas as tabelas e todos os dados da conexão configurada. Confira o `.env` e nunca use esse comando em um banco compartilhado ou com dados importantes.

Ao executar `LibrarySeeder` novamente, as seis categorias são reaproveitadas por `firstOrCreate()`, mas novos autores, livros, detalhes e associações são acrescentados.

## 9. Conferindo os dados com o Eloquent

Abra o Tinker:

```bash
php artisan tinker
```

Confira as quantidades:

```php
App\Models\Author::count();       // 8 na primeira execução
App\Models\Book::count();         // 40 na primeira execução
App\Models\BookDetail::count();   // 40 na primeira execução
App\Models\Category::count();     // 6 na primeira execução
```

Carregue um livro e seus três tipos de relacionamento:

```php
$book = App\Models\Book::with([
    'author',
    'detail',
    'categories',
])->first();

$book->title;
$book->author->name;
$book->detail?->pages;
```

Confira os dados da pivot:

```php
$book->categories->map(fn ($category) => [
    'name' => $category->name,
    'featured' => (bool) $category->pivot->featured,
    'position' => $category->pivot->position,
]);
```

Veja quantos livros cada autor possui:

```php
App\Models\Author::withCount('books')
    ->orderBy('name')
    ->get(['id', 'name']);
```

Com o banco populado, você também pode executar o [exemplo de lazy e eager loading](../06-orm-eloquent/exemplos/) e abrir o CRUD da próxima apostila de [Controllers](../08-controllers/).

## 10. Cuidados e erros frequentes

| Situação | Causa provável | Como conferir |
|---|---|---|
| `Call to undefined method ...::factory()` | O Model não usa `HasFactory` | Adicione a trait ao Model correspondente |
| Erro de tabela ou coluna inexistente | Migrations pendentes ou estrutura diferente | Execute `php artisan migrate:status` e compare os campos |
| Erro de chave estrangeira | Registro pai não existe ou relação está configurada incorretamente | Crie os pais antes dos filhos e confira `for()` e `has()` |
| Erro na pivot | Migration e `belongsToMany()` representam versões diferentes | Alinhe colunas extras e `withTimestamps()` |
| ISBN duplicado | Valor repetido em coluna única | Mantenha a constraint do banco e gere valores adequados |
| Quantidades maiores que as previstas | O Seeder foi executado mais de uma vez | Lembre que a nova execução acrescenta registros |

Boas práticas para este primeiro contato:

- use nomes de Factory e Model compatíveis, como `BookFactory` e `Book`;
- mantenha um Seeder principal pequeno e chame Seeders especializados com `call()`;
- prefira valores fixos para referências que precisam ser reconhecidas na aula;
- use Factories para gerar volume e variedade;
- respeite a ordem exigida pelas chaves estrangeiras;
- mantenha dados fictícios fora de ambientes com dados reais;
- trate as constraints do banco como a garantia final de integridade.

`fake()->unique()` ajuda durante a geração atual, mas não substitui uma constraint `unique()` criada pela migration.

## 11. Quadro de consulta rápida

| Elemento | Lembrete |
|---|---|
| Factory | Receita de atributos para um Model |
| Seeder | Coordena quais dados serão inseridos |
| `definition()` | Devolve os valores padrão da Factory |
| `fake()` | Gera dados fictícios |
| `HasFactory` | Disponibiliza `Model::factory()` |
| `make()` | Cria o objeto sem persistir |
| `create()` | Cria e persiste o Model |
| `count()` | Define a quantidade de registros |
| `for()` | Associa o Model criado ao lado pai de `belongsTo` |
| `has()` | Cria registros pelo relacionamento do Model pai |
| `attach()` | Insere associações na tabela pivot |
| `firstOrCreate()` | Reutiliza um registro existente ou cria outro |
| `call()` | Executa outro Seeder |
| `db:seed` | Executa o `DatabaseSeeder` |
| `migrate:fresh --seed` | Apaga, recria e popula o banco |

## O que você precisa guardar

1. Migrations definem a estrutura; Seeders e Factories inserem dados nessa estrutura.
2. Uma Factory descreve valores padrão; um Seeder organiza o cenário completo.
3. Os Models precisam utilizar `HasFactory` para oferecer `factory()`.
4. `make()` não grava; `create()` grava no banco.
5. `for()` preenche a relação inversa, como o autor de cada livro.
6. `has()` cria registros relacionados, como os detalhes do livro.
7. `attach()` preenche a pivot e pode receber atributos adicionais.
8. O `DatabaseSeeder` pode chamar Seeders menores em uma ordem explícita.
9. Executar novamente um Seeder pode acrescentar registros.
10. `migrate:fresh --seed` é útil somente quando o banco pode ser completamente apagado.

## Referências

- [Laravel 13 — Database Seeding](https://laravel.com/docs/13.x/seeding)
- [Laravel 13 — Eloquent Factories](https://laravel.com/docs/13.x/eloquent-factories)
- [Laravel 13 — Relacionamentos do Eloquent](https://laravel.com/docs/13.x/eloquent-relationships)
- [FakerPHP — documentação](https://fakerphp.org/)
