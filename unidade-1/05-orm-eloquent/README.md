# Models no Laravel: representando e relacionando dados

Na apostila de Migrations, criamos a tabela `books`:

```php
Schema::create('books', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('isbn', 13)->unique();
    $table->unsignedSmallInteger('published_year')->nullable();
    $table->timestamps();
});
```

A migration define a estrutura da tabela. Para representar e manipular os livros dentro da aplicação, utilizaremos um **Model**.

## Índice

1. [O que é um Model?](#1-o-que-é-um-model)
2. [Criando o Model Book](#2-criando-o-model-book)
3. [Por que não existem propriedades, getters e setters comuns?](#3-por-que-não-existem-propriedades-getters-e-setters-comuns)
4. [Convenções de nomes](#4-convenções-de-nomes)
5. [Relacionamentos entre Models](#5-relacionamentos-entre-models)
6. [Relacionamento um para um](#6-relacionamento-um-para-um)
7. [Relacionamento um para muitos](#7-relacionamento-um-para-muitos)
8. [Relacionamento muitos para muitos](#8-relacionamento-muitos-para-muitos)
9. [Colunas adicionais na tabela pivot](#9-colunas-adicionais-na-tabela-pivot)
10. [O que pode ser colocado no Model?](#10-o-que-pode-ser-colocado-no-model)
11. [O que não deve ficar no Model?](#11-o-que-não-deve-ficar-no-model)
12. [Quadro de consulta rápida](#12-quadro-de-consulta-rápida)

## 1. O que é um Model?

Um **Model** é uma classe que representa os dados e os comportamentos relacionados a uma entidade da aplicação.

Em um sistema de biblioteca, podemos ter:

| Entidade | Model | Tabela |
|---|---|---|
| Livro | `Book` | `books` |
| Autor | `Author` | `authors` |
| Categoria | `Category` | `categories` |
| Detalhes do livro | `BookDetail` | `book_details` |

Normalmente, cada objeto de um Model representa um registro da tabela correspondente.

Considere este registro de `books`:

| id | title | isbn | published_year |
|---:|---|---|---:|
| 1 | O Hobbit | 9788595084742 | 1937 |

Dentro da aplicação, ele pode ser representado por um objeto:

```php
$book = Book::find(1);

echo $book->title;
```

O Laravel utiliza o **Eloquent**, seu sistema de mapeamento objeto-relacional, para fazer a ligação entre Models e tabelas.

| Elemento | Responsabilidade |
|---|---|
| Migration | Define a estrutura do banco |
| Tabela | Armazena os registros |
| Model | Representa os dados e comportamentos |
| Eloquent | Faz a ligação entre Models e banco |

O Model pode ser utilizado para:

- representar registros;
- definir relacionamentos;
- transformar atributos;
- realizar cálculos;
- implementar regras relacionadas à entidade;
- acessar os dados por meio do Eloquent.

As operações de consulta e persistência serão aprofundadas na próxima apostila.

## 2. Criando o Model `Book`

Os Models normalmente ficam em:

```text
app/Models
```

Para criar o Model `Book`:

```bash
php artisan make:model Book
```

O comando cria:

```text
app/Models/Book.php
```

Model inicial:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'isbn', 'published_year'])]
class Book extends Model
{
    //
}
```

Por convenção, o Eloquent associa:

```text
Model Book → tabela books
```

### Primeiro exemplo

Podemos criar um objeto e atribuir valores:

```php
$book = new Book();

$book->title = 'O Hobbit';
$book->isbn = '9788595084742';
$book->published_year = 1937;

$book->save();
```

O objeto representa um livro. O método `save()` solicita ao Eloquent que grave seus dados na tabela.

Também podemos recuperar um registro:

```php
$book = Book::find(1);

echo $book->title;
```

## 3. Por que não existem propriedades, getters e setters comuns?

A tabela `books` possui colunas como:

```text
id
title
isbn
published_year
created_at
updated_at
```

Entretanto, o Model não declara:

```php
private int $id;
private string $title;
private string $isbn;
```

Também não possui métodos como:

```php
getTitle()
setTitle()
getIsbn()
setIsbn()
```

Isso acontece porque o Eloquent utiliza **atributos dinâmicos**.

### Como os valores são armazenados?

A classe `Book` herda da classe `Model` uma estrutura interna chamada `$attributes`.

De maneira simplificada, os dados são mantidos assim:

```php
[
    'id' => 1,
    'title' => 'O Hobbit',
    'isbn' => '9788595084742',
    'published_year' => 1937,
]
```

Quando escrevemos:

```php
echo $book->title;
```

o Eloquent procura `title` entre os atributos do objeto.

Quando escrevemos:

```php
$book->title = 'Novo título';
```

o Eloquent atualiza esse atributo.

Simplificando o funcionamento interno:

```text
$book->title
    ↓
O Model procura o atributo title
    ↓
O valor é devolvido
```

```text
$book->title = 'Novo título'
    ↓
O Model recebe o novo valor
    ↓
O atributo title é atualizado
```

A classe base `Model` já fornece esse comportamento. Por isso, não precisamos declarar uma propriedade e criar getters e setters simples para cada coluna.

### A migration continua definindo a estrutura

O Model não lê os arquivos de migrations a cada consulta. Quando o banco devolve um registro, o Eloquent coloca os valores encontrados nos atributos do objeto.

A estrutura válida continua sendo determinada pela tabela.

Se tentarmos salvar um atributo que não possui coluna correspondente, o banco poderá rejeitar a operação.

### `Fillable` não declara colunas

Esta configuração:

```php
#[Fillable(['title', 'isbn', 'published_year'])]
```

não cria as colunas e não descreve toda a tabela.

Ela apenas informa quais atributos podem ser preenchidos em conjunto:

```php
Book::create([
    'title' => 'O Hobbit',
    'isbn' => '9788595084742',
    'published_year' => 1937,
]);
```

Portanto:

```text
Migration → define quais colunas existem
Fillable  → autoriza o preenchimento em conjunto
```

### Quando precisamos personalizar a atribuição?

Quando um valor precisa ser transformado, podemos utilizar um **mutator**.

Exemplo: retirar espaços e hífens do ISBN antes de armazená-lo:

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function isbn(): Attribute
{
    return Attribute::make(
        set: fn (string $value) =>
            str_replace(['-', ' '], '', $value),
    );
}
```

Assim:

```text
978-85-9508-474-2
```

será armazenado como:

```text
9788595084742
```

Não precisamos criar esse tipo de método para todas as colunas. Ele é utilizado somente quando existe alguma transformação necessária.

## 4. Convenções de nomes

O Eloquent utiliza convenções para associar Models, tabelas e chaves.

### Model e tabela

O Model normalmente utiliza:

- nome no singular;
- `StudlyCase`;
- arquivo com o mesmo nome da classe.

A tabela utiliza:

- nome no plural;
- letras minúsculas;
- `snake_case`.

| Model | Arquivo | Tabela |
|---|---|---|
| `Book` | `Book.php` | `books` |
| `Author` | `Author.php` | `authors` |
| `Category` | `Category.php` | `categories` |
| `BookDetail` | `BookDetail.php` | `book_details` |

### Chaves e datas

Por padrão, o Eloquent espera:

| Elemento | Convenção |
|---|---|
| Chave primária | `id` |
| Chave estrangeira de `Author` | `author_id` |
| Chave estrangeira de `Book` | `book_id` |
| Data de criação | `created_at` |
| Data de atualização | `updated_at` |

A migration segue essas convenções com:

```php
$table->id();
$table->foreignId('author_id');
$table->timestamps();
```

### Tabela fora da convenção

Se `Book` utilizar uma tabela chamada `library_books`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('library_books')]
class Book extends Model
{
    //
}
```

Se a chave primária se chamar `book_code`:

```php
#[Table('library_books', key: 'book_code')]
class Book extends Model
{
    //
}
```

Se a tabela não possuir `created_at` e `updated_at`:

```php
#[Table('library_books', timestamps: false)]
class Book extends Model
{
    //
}
```

### Chave estrangeira fora da convenção

Se `books` utilizar `writer_id` em vez de `author_id`:

```php
public function author(): BelongsTo
{
    return $this->belongsTo(Author::class, 'writer_id');
}
```

Sempre que possível, siga as convenções. Elas reduzem configurações e facilitam a compreensão do projeto.

## 5. Relacionamentos entre Models

As entidades de um sistema normalmente possuem relacionamentos.

Na biblioteca:

- um livro pode possuir detalhes;
- um autor pode possuir vários livros;
- um livro pode pertencer a várias categorias.

No Eloquent, os relacionamentos são definidos como métodos nos Models.

| Cardinalidade | Métodos |
|---|---|
| Um para um | `hasOne` e `belongsTo` |
| Um para muitos | `hasMany` e `belongsTo` |
| Muitos para muitos | `belongsToMany` |

A migration e o Model possuem funções diferentes:

```text
Migration
Cria as colunas, chaves e restrições no banco
```

```text
Model
Informa ao Eloquent como utilizar a relação
```

Definir um método no Model não cria a chave estrangeira. Criar a chave estrangeira também não cria automaticamente os métodos nos Models.

## 6. Relacionamento um para um

Vamos considerar que cada livro pode possuir um registro de detalhes:

```text
Book 1 ─── 1 BookDetail
```

### Estrutura

```text
books
- id
- title
- isbn
- published_year

book_details
- id
- book_id
- pages
- summary
```

A chave estrangeira fica em `book_details`.

### Migration de `book_details`

```php
public function up(): void
{
    Schema::create('book_details', function (Blueprint $table) {
        $table->id();

        $table->foreignId('book_id')
            ->unique()
            ->constrained()
            ->cascadeOnDelete();

        $table->unsignedInteger('pages')->nullable();
        $table->text('summary')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('book_details');
}
```

A chave estrangeira garante que `book_id` referencia um livro existente.

A restrição `unique()` impede que o mesmo livro apareça várias vezes em `book_details`.

> `hasOne()` representa a relação no código. `unique()` garante no banco que um livro não tenha vários registros de detalhes.

Essa estrutura permite que um livro tenha zero ou um detalhe. A aplicação pode exigir a criação dos detalhes caso eles sejam obrigatórios.

### Model `Book`

```php
use Illuminate\Database\Eloquent\Relations\HasOne;

class Book extends Model
{
    public function detail(): HasOne
    {
        return $this->hasOne(BookDetail::class);
    }
}
```

### Model `BookDetail`

```php
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['book_id', 'pages', 'summary'])]
class BookDetail extends Model
{
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
```

O lado que contém a chave estrangeira utiliza `belongsTo`:

```text
BookDetail contém book_id
BookDetail belongsTo Book
Book hasOne BookDetail
```

### Acessando a relação

```php
$book = Book::find(1);

echo $book->detail?->pages;
```

No sentido contrário:

```php
$detail = BookDetail::find(1);

echo $detail->book->title;
```

## 7. Relacionamento um para muitos

Um autor pode escrever vários livros. Cada livro pertence a um autor:

```text
Author 1 ─── N Book
```

A chave estrangeira fica no lado “muitos”:

```text
books.author_id
```

### Migration de `authors`

```php
public function up(): void
{
    Schema::create('authors', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('authors');
}
```

### Adicionando `author_id` a `books`

Como a tabela `books` já existe, criamos outra migration:

```bash
php artisan make:migration add_author_id_to_books_table --table=books
```

```php
public function up(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->foreignId('author_id')
            ->constrained()
            ->restrictOnDelete();
    });
}

public function down(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->dropForeign(['author_id']);
        $table->dropColumn('author_id');
    });
}
```

`restrictOnDelete()` impede que um autor seja apagado enquanto possuir livros.

Se já existirem livros sem autor, podemos começar com uma relação opcional:

```php
$table->foreignId('author_id')
    ->nullable()
    ->constrained()
    ->nullOnDelete();
```

### Model `Author`

```php
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Author extends Model
{
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
```

### Model `Book`

```php
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'isbn',
    'published_year',
    'author_id',
])]
class Book extends Model
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
```

O método `books()` fica no plural porque um autor pode possuir vários livros. `author()` fica no singular porque cada livro possui um autor.

### Acessando a relação

```php
$author = Author::find(1);

foreach ($author->books as $book) {
    echo $book->title;
}
```

```php
$book = Book::find(1);

echo $book->author->name;
```

## 8. Relacionamento muitos para muitos

Um livro pode pertencer a várias categorias. Uma categoria também pode conter vários livros:

```text
Book N ─── N Category
```

Esse relacionamento precisa de uma terceira tabela, chamada **tabela intermediária** ou **tabela pivot**:

```text
book_category
```

### Migration de `categories`

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('categories');
}
```

### Migration da pivot

```php
public function up(): void
{
    Schema::create('book_category', function (Blueprint $table) {
        $table->foreignId('book_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('category_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->primary(['book_id', 'category_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('book_category');
}
```

Cada registro representa uma associação:

| book_id | category_id |
|---:|---:|
| 1 | 2 |
| 1 | 4 |
| 3 | 2 |

A chave primária composta impede que a mesma categoria seja associada duas vezes ao mesmo livro.

### Nome da tabela pivot

Por convenção, o Eloquent utiliza os nomes dos Models:

- no singular;
- em `snake_case`;
- em ordem alfabética.

Para `Book` e `Category`:

```text
book_category
```

### Model `Book`

```php
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
```

### Model `Category`

```php
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Category extends Model
{
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}
```

Os dois lados utilizam `belongsToMany()`.

### Acessando a relação

```php
foreach ($book->categories as $category) {
    echo $category->name;
}
```

```php
foreach ($category->books as $book) {
    echo $book->title;
}
```

Para criar uma associação:

```php
$book->categories()->attach($categoryId);
```

Para removê-la:

```php
$book->categories()->detach($categoryId);
```

Essas operações alteram a pivot. Elas não apagam os livros ou as categorias.

### Pivot fora da convenção

Se a tabela se chamar `book_classifications`:

```php
public function categories(): BelongsToMany
{
    return $this->belongsToMany(
        Category::class,
        'book_classifications'
    );
}
```

Também é possível informar chaves diferentes:

```php
return $this->belongsToMany(
    Category::class,
    'book_classifications',
    'book_code',
    'category_code'
);
```

## 9. Colunas adicionais na tabela pivot

Uma pivot também pode armazenar informações sobre a associação.

Podemos registrar:

- se uma categoria está em destaque para determinado livro;
- a ordem em que ela deve aparecer.

Se `book_category` já existir, criamos uma nova migration:

```php
public function up(): void
{
    Schema::table('book_category', function (Blueprint $table) {
        $table->boolean('featured')->default(false);
        $table->unsignedSmallInteger('position')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::table('book_category', function (Blueprint $table) {
        $table->dropColumn(['featured', 'position']);
        $table->dropTimestamps();
    });
}
```

Agora a pivot poderá conter:

| book_id | category_id | featured | position |
|---:|---:|---:|---:|
| 1 | 2 | 1 | 1 |
| 1 | 4 | 0 | 2 |

`featured` e `position` descrevem a associação entre o livro e a categoria.

### Configuração nos Models

No Model `Book`:

```php
public function categories(): BelongsToMany
{
    return $this->belongsToMany(Category::class)
        ->withPivot('featured', 'position')
        ->withTimestamps();
}
```

No Model `Category`:

```php
public function books(): BelongsToMany
{
    return $this->belongsToMany(Book::class)
        ->withPivot('featured', 'position')
        ->withTimestamps();
}
```

`withPivot()` informa quais colunas adicionais devem ser carregadas.

`withTimestamps()` corresponde às colunas `created_at` e `updated_at` adicionadas à pivot.

### Criando e acessando a associação

```php
$book->categories()->attach($categoryId, [
    'featured' => true,
    'position' => 1,
]);
```

```php
foreach ($book->categories as $category) {
    echo $category->name;
    echo $category->pivot->featured;
    echo $category->pivot->position;
}
```

Uma pivot simples não precisa de Model próprio. Um Model intermediário pode ser criado posteriormente se a associação passar a possuir muitas regras e comportamentos.

## 10. O que pode ser colocado no Model?

O Model pode reunir operações relacionadas aos dados e ao comportamento da entidade.

### Relacionamentos

```php
public function author(): BelongsTo
{
    return $this->belongsTo(Author::class);
}

public function categories(): BelongsToMany
{
    return $this->belongsToMany(Category::class);
}
```

### Transformações de atributos

```php
protected function title(): Attribute
{
    return Attribute::make(
        set: fn (string $value) => trim($value),
    );
}
```

### Cálculos

```php
public function publicationAge(int $referenceYear): ?int
{
    if ($this->published_year === null) {
        return null;
    }

    return $referenceYear - $this->published_year;
}
```

### Regras relacionadas à entidade

```php
public function isClassic(
    int $referenceYear,
    int $minimumAge = 50
): bool {
    $age = $this->publicationAge($referenceYear);

    return $age !== null && $age >= $minimumAge;
}
```

Utilização:

```php
if ($book->isClassic(2026)) {
    echo 'O livro é considerado um clássico';
}
```

A classificação fica no Model porque depende dos dados e do significado de um livro.

## 11. O que não deve ficar no Model?

O Model não deve assumir responsabilidades das outras camadas.

Evite colocar nele operações como:

- ler diretamente `$_GET` ou `$_POST`;
- controlar a requisição HTTP;
- produzir HTML;
- retornar uma View;
- redirecionar o usuário;
- controlar mensagens de sessão.

Distribuição recomendada:

| Responsabilidade | Local mais adequado |
|---|---|
| Receber a requisição | Controller |
| Validar os campos enviados | Form Request ou Controller |
| Coordenar uma operação | Controller |
| Redirecionar o usuário | Controller |
| Produzir HTML | View |
| Definir relacionamentos | Model |
| Transformar atributos | Model |
| Calcular informações da entidade | Model |
| Aplicar regras da entidade | Model ou classe de serviço |

O objetivo não é colocar todo o código no Model. É manter cada responsabilidade no local mais adequado.

## 12. Quadro de consulta rápida

| Conceito | Lembrete |
|---|---|
| Model | Representa dados e comportamentos de uma entidade |
| Eloquent | Faz a ligação entre Models e banco |
| Atributos dinâmicos | Valores mantidos internamente pelo Model |
| `Fillable` | Autoriza o preenchimento em conjunto |
| `Book` | Nome convencional do Model |
| `books` | Nome convencional da tabela |
| `id` | Chave primária convencional |
| `author_id` | Chave estrangeira convencional |
| `hasOne` | Possui um registro relacionado |
| `hasMany` | Possui vários registros relacionados |
| `belongsTo` | Pertence ao registro relacionado |
| `belongsToMany` | Relacionamento muitos para muitos |
| Pivot | Tabela intermediária |
| `withPivot()` | Carrega colunas adicionais da pivot |
| `attach()` | Cria uma associação na pivot |
| `detach()` | Remove uma associação da pivot |

## O que você precisa guardar

1. Um Model representa os dados e comportamentos de uma entidade.
2. A migration define a estrutura; o Model permite trabalhar com os dados.
3. O Eloquent mantém os valores em atributos dinâmicos.
4. Não precisamos declarar uma propriedade, um getter e um setter para cada coluna.
5. `Fillable` não cria colunas; ele autoriza o preenchimento em conjunto.
6. Seguir as convenções reduz configurações.
7. A migration cria chaves e restrições; o Model descreve como utilizar a relação.
8. No relacionamento um para um, `unique()` impede vários registros relacionados.
9. No relacionamento um para muitos, a chave estrangeira fica no lado “muitos”.
10. O relacionamento muitos para muitos utiliza uma tabela pivot.
11. Colunas adicionais da pivot devem ser informadas com `withPivot()`.
12. Relacionamentos, transformações, cálculos e regras da entidade podem ficar no Model.
13. Requisições, redirecionamentos e HTML pertencem a outras camadas.

## Referências

- [Laravel 13 — Eloquent](https://laravel.com/docs/13.x/eloquent)
- [Laravel 13 — Relacionamentos do Eloquent](https://laravel.com/docs/13.x/eloquent-relationships)
- [Laravel 13 — Accessors, mutators e conversões](https://laravel.com/docs/13.x/eloquent-mutators)
