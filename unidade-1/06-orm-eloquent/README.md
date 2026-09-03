# ORM Eloquent: manipulando os dados da biblioteca

Nas apostilas anteriores, utilizamos **migrations** para criar as tabelas e **Models** para representar entidades como livros, autores e categorias.

Agora utilizaremos o **Eloquent**, o ORM do Laravel, para consultar e modificar os registros dessas tabelas.

Nesta apostila, continuaremos com o sistema de biblioteca e veremos como realizar as operações de cadastro, consulta, atualização e remoção de livros.

## Índice

1. [O que é um ORM?](#1-o-que-é-um-orm)
2. [Migration, Model e ORM](#2-migration-model-e-orm)
3. [O Eloquent do Laravel](#3-o-eloquent-do-laravel)
4. [Construindo e executando consultas](#4-construindo-e-executando-consultas)
5. [Consultas mais utilizadas](#5-consultas-mais-utilizadas)
6. [CRUD com Eloquent](#6-crud-com-eloquent)
7. [Trabalhando com relacionamentos](#7-trabalhando-com-relacionamentos)
8. [Lazy loading e eager loading](#8-lazy-loading-e-eager-loading)
9. [Paginação em um Controller](#9-paginação-em-um-controller)
10. [Cuidados e boas práticas](#10-cuidados-e-boas-práticas)
11. [Quadro de consulta rápida](#11-quadro-de-consulta-rápida)

## 1. O que é um ORM?

Um banco de dados relacional organiza informações em:

- tabelas;
- colunas;
- registros;
- chaves e relacionamentos.

Uma aplicação orientada a objetos trabalha com:

- classes;
- objetos;
- atributos;
- métodos.

Um **ORM** realiza o **mapeamento objeto-relacional** entre essas duas formas de representar os dados.

| Banco de dados | Aplicação |
|---|---|
| Tabela `books` | Classe `Book` |
| Registro de um livro | Objeto `$book` |
| Coluna `title` | Atributo `$book->title` |
| Chave `author_id` | Relacionamento `$book->author` |

Podemos pensar no ORM como um **intérprete** entre a aplicação e o banco:

1. o desenvolvedor utiliza classes e métodos;
2. o ORM transforma a operação em comandos compreendidos pelo banco;
3. o banco devolve registros;
4. o ORM transforma os registros em objetos.

Por exemplo:

```php
$book = Book::find(1);

echo $book->title;
```

O Eloquent consulta a tabela `books` e representa o registro encontrado por meio de um objeto da classe `Book`.

### Benefícios de um ORM

Um ORM pode oferecer:

- menos código repetitivo de conexão e conversão de dados;
- consultas escritas com a linguagem da aplicação;
- objetos que representam os registros;
- uma forma padronizada de inserir, consultar, atualizar e remover dados;
- suporte a relacionamentos entre entidades;
- código mais legível e fácil de manter.

O ORM reduz a necessidade de escrever SQL diretamente em tarefas comuns, mas **não elimina a importância de conhecer bancos de dados e SQL**. Esse conhecimento continua necessário para compreender relacionamentos, restrições, índices, consultas e desempenho.

### ORMs em diferentes tecnologias

O conceito não é exclusivo do Laravel.

| Tecnologia | Exemplos de ORM |
|---|---|
| PHP | [Eloquent](https://laravel.com/docs/13.x/eloquent) e [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html) |
| Java | [Hibernate ORM](https://hibernate.org/orm/) |
| JavaScript e TypeScript | [Sequelize](https://sequelize.org/) e [TypeORM](https://typeorm.io/) |
| Python | [Django ORM](https://docs.djangoproject.com/en/stable/topics/db/) e [SQLAlchemy ORM](https://docs.sqlalchemy.org/orm/) |
| C# e .NET | [Entity Framework Core](https://learn.microsoft.com/ef/core/) |
| Ruby | [Active Record](https://guides.rubyonrails.org/active_record_basics.html) |

Cada ferramenta possui sua própria sintaxe e suas próprias convenções, mas todas procuram aproximar os objetos da aplicação dos dados relacionais.

## 2. Migration, Model e ORM

Migration e ORM trabalham com o banco de dados, mas possuem responsabilidades diferentes.

Uma analogia com a biblioteca ajuda a separar os papéis:

- a **migration** monta e modifica as estantes;
- o **Model** representa cada tipo de item guardado;
- o **ORM** permite guardar, localizar, alterar e retirar os itens.

| Elemento | Responsabilidade | Exemplo |
|---|---|---|
| Migration | Define e modifica a estrutura | Criar a tabela `books` e a coluna `title` |
| Model | Representa uma entidade na aplicação | Classe `Book` |
| ORM | Manipula os registros por meio dos Models | Consultar ou cadastrar um livro |

Na migration:

```php
Schema::create('books', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('isbn', 13)->unique();
    $table->unsignedSmallInteger('published_year')->nullable();
    $table->foreignId('author_id')->constrained();
    $table->timestamps();
});
```

Essa operação cria a estrutura da tabela.

Com o Eloquent:

```php
Book::create([
    'title' => 'O Hobbit',
    'isbn' => '9788595084742',
    'published_year' => 1937,
    'author_id' => 1,
]);
```

Essa operação cria um **registro** dentro da estrutura existente.

> Migration altera o esquema do banco. Eloquent trabalha principalmente com os dados armazenados nesse esquema.

Criar um relacionamento no Model também não cria sua chave estrangeira. A chave deve existir no banco por meio de uma migration.

## 3. O Eloquent do Laravel

O **Eloquent** é o ORM incluído no Laravel. Cada tabela possui um Model correspondente, utilizado para consultar, inserir, atualizar e remover registros.

No sistema de biblioteca:

| Model | Tabela |
|---|---|
| `Book` | `books` |
| `Author` | `authors` |
| `Category` | `categories` |

Um objeto de `Book` normalmente representa um registro de `books`:

```php
$book = Book::find(1);

echo $book->title;
echo $book->isbn;
```

Continuaremos utilizando o Model definido na apostila anterior:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withPivot('featured', 'position')
            ->withTimestamps();
    }
}
```

O atributo `Fillable` permite o preenchimento em conjunto dos campos informados. Ele **não cria colunas e não realiza validação**.

## 4. Construindo e executando consultas

Uma consulta do Eloquent pode ser construída em etapas.

```php
$query = Book::where('published_year', '>=', 2000);

$query->orderBy('title');
```

Nesse momento, `$query` representa uma consulta sendo preparada. Para obter os livros, precisamos executá-la:

```php
$books = $query->get();
```

Podemos escrever a mesma operação por encadeamento:

```php
$books = Book::where('published_year', '>=', 2000)
    ->orderBy('title')
    ->get();
```

Cada método acrescenta uma parte à consulta. `get()` executa a consulta e devolve os resultados.

### O tipo de resultado

| Finalização | Resultado |
|---|---|
| `get()` | Coleção de Models, mesmo que esteja vazia |
| `first()` | Um Model ou `null` |
| `find($id)` | Um Model ou `null` |
| `findOrFail($id)` | Um Model ou uma exceção de registro não encontrado |
| `count()` | Um número |
| `exists()` | `true` ou `false` |
| `paginate(10)` | Um paginador com até 10 registros por página |

Uma **coleção** é um objeto que reúne vários Models e pode ser percorrido com `foreach`:

```php
foreach ($books as $book) {
    echo $book->title;
}
```

## 5. Consultas mais utilizadas

### Todos os registros

```php
$books = Book::all();
```

`all()` é simples, mas deve ser evitado quando a tabela pode possuir muitos registros. Nesses casos, utilize filtros ou paginação.

### Registro pela chave primária

```php
$book = Book::find(1);
```

Quando o registro precisa obrigatoriamente existir, podemos utilizar:

```php
$book = Book::findOrFail(1);
```

Em uma aplicação Web, `findOrFail()` permite que o Laravel produza a resposta de “não encontrado” quando não existe um registro com aquele identificador.

### Filtros com `where`

```php
$books = Book::where('published_year', '>=', 2000)->get();
```

Várias condições podem ser combinadas:

```php
$books = Book::where('author_id', 1)
    ->where('published_year', '>=', 2000)
    ->get();
```

Busca por parte do título:

```php
$books = Book::where('title', 'like', '%Laravel%')->get();
```

### Primeiro resultado

```php
$book = Book::where('isbn', '9788595084742')->first();
```

`first()` devolve somente o primeiro resultado ou `null`.

### Ordenação e limite

```php
$books = Book::orderBy('published_year', 'desc')
    ->take(10)
    ->get();
```

O código recupera até dez livros, começando pelos mais recentes.

### Agregações

```php
$total = Book::count();
$media = Book::avg('published_year');
$maisAntigo = Book::min('published_year');
$maisRecente = Book::max('published_year');
```

As agregações devolvem valores calculados, não objetos `Book`.

Também podemos combinar filtro e agregação:

```php
$totalRecentes = Book::where('published_year', '>=', 2000)->count();
```

## 6. CRUD com Eloquent

**CRUD** reúne as quatro operações básicas realizadas com dados.

| Letra | Operação | Significado | SQL correspondente |
|---|---|---|---|
| C | Create | Criar | `INSERT` |
| R | Read | Consultar | `SELECT` |
| U | Update | Atualizar | `UPDATE` |
| D | Delete | Remover | `DELETE` |

O Eloquent permite realizar essas operações por meio do Model.

### Create: cadastrar um livro

```php
$book = Book::create([
    'title' => 'O Hobbit',
    'isbn' => '9788595084742',
    'published_year' => 1937,
    'author_id' => 1,
]);
```

`create()` grava o registro e devolve o Model criado.

Como recebe vários atributos de uma vez, os campos precisam estar autorizados pelo `Fillable` do Model.

Outra forma é criar o objeto, atribuir seus valores e chamar `save()`:

```php
$book = new Book();
$book->title = 'Dom Casmurro';
$book->isbn = '9788535910663';
$book->published_year = 1899;
$book->author_id = 2;
$book->save();
```

### Read: consultar livros

```php
$books = Book::orderBy('title')->get();

$book = Book::findOrFail(1);
```

O primeiro comando devolve uma coleção. O segundo devolve um único livro.

### Update: atualizar um livro

```php
$book = Book::findOrFail(1);

$book->title = 'O Hobbit - edição revisada';
$book->save();
```

Também podemos atualizar vários atributos autorizados:

```php
$book->update([
    'title' => 'O Hobbit - edição revisada',
    'published_year' => 1937,
]);
```

`save()` insere quando o objeto é novo e atualiza quando o objeto já corresponde a um registro existente.

### Delete: remover um livro

```php
$book = Book::findOrFail(1);
$book->delete();
```

O comportamento dos registros relacionados também depende das chaves estrangeiras definidas nas migrations. Por exemplo, `cascadeOnDelete()`, `nullOnDelete()` e `restrictOnDelete()` produzem efeitos diferentes.

### Validação não é responsabilidade do ORM

Antes de salvar dados recebidos em uma requisição, o Controller deve validá-los:

```php
$validated = $request->validate([
    'title' => ['required', 'string', 'max:255'],
    'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
    'published_year' => ['nullable', 'integer', 'between:1000,2100'],
    'author_id' => ['required', 'exists:authors,id'],
]);

$book = Book::create($validated);
```

`validate()` verifica os dados. `Fillable` controla quais atributos podem ser preenchidos em conjunto. São mecanismos diferentes.

## 7. Trabalhando com relacionamentos

Os relacionamentos foram definidos nos Models da apostila anterior. Agora podemos utilizá-los para acessar e manipular dados relacionados.

### Acessando dados relacionados

Um livro pertence a um autor:

```php
$book = Book::findOrFail(1);

echo $book->author->name;
```

Um autor possui vários livros:

```php
$author = Author::findOrFail(1);

foreach ($author->books as $book) {
    echo $book->title;
}
```

Um livro pertence a várias categorias:

```php
foreach ($book->categories as $category) {
    echo $category->name;
}
```

### Propriedade ou método?

As duas formas têm objetivos diferentes:

```php
$book->categories;
$book->categories();
```

| Forma | Resultado |
|---|---|
| `$book->categories` | Coleção de categorias relacionadas |
| `$book->categories()` | Objeto de relacionamento usado para consultar ou modificar a relação |

Podemos filtrar os livros de um autor utilizando o método:

```php
$books = $author->books()
    ->where('published_year', '>=', 2000)
    ->orderBy('title')
    ->get();
```

### Criando um registro relacionado

```php
$author = Author::findOrFail(1);

$book = $author->books()->create([
    'title' => 'Novo livro',
    'isbn' => '9781234567890',
    'published_year' => 2026,
]);
```

O Eloquent preenche `author_id` com o identificador do autor.

Para um relacionamento um para um:

```php
$book->detail()->create([
    'pages' => 320,
    'summary' => 'Resumo do livro.',
]);
```

### Manipulando uma relação muitos para muitos

Associar uma categoria ao livro:

```php
$book->categories()->attach($categoryId);
```

Remover somente a associação:

```php
$book->categories()->detach($categoryId);
```

Manter exatamente as categorias informadas:

```php
$book->categories()->sync([1, 3, 5]);
```

`sync()` cria as associações ausentes e remove as que não aparecem na lista. Os livros e as categorias continuam no banco; somente os registros da tabela pivot são modificados.

Se a pivot possui colunas adicionais:

```php
$book->categories()->attach($categoryId, [
    'featured' => true,
    'position' => 1,
]);
```

Após configurar `withPivot()` no relacionamento, os valores podem ser acessados por:

```php
foreach ($book->categories as $category) {
    echo $category->name;
    echo $category->pivot->featured;
    echo $category->pivot->position;
}
```

## 8. Lazy loading e eager loading

Ao consultar livros, os relacionamentos podem ser carregados em momentos diferentes.

### Lazy loading

No **lazy loading**, ou carregamento preguiçoso, o relacionamento é consultado somente quando sua propriedade é acessada.

```php
$books = Book::all();

foreach ($books as $book) {
    echo $book->author->name;
}
```

Primeiro, o Eloquent consulta os livros. Durante o `foreach`, pode executar uma nova consulta para encontrar o autor de cada livro.

Se existirem 25 livros, esse código poderá produzir:

- uma consulta para os livros;
- 25 consultas para os autores.

Esse é o problema conhecido como **N + 1 consultas**.

### Eager loading

No **eager loading**, ou carregamento antecipado, informamos antes da execução quais relacionamentos serão necessários:

```php
$books = Book::with('author')->get();

foreach ($books as $book) {
    echo $book->author->name;
}
```

Nesse caso, o Eloquent normalmente realiza:

- uma consulta para os livros;
- uma consulta para todos os autores necessários.

Também podemos carregar vários relacionamentos:

```php
$books = Book::with(['author', 'categories'])->get();
```

> Eager loading não significa necessariamente utilizar um único `JOIN`. Para esse exemplo, o Eloquent normalmente executa consultas separadas e reúne os resultados nos Models.

### Quando utilizar cada um?

| Situação | Estratégia |
|---|---|
| Precisaremos do relacionamento para vários registros | Prefira eager loading |
| Talvez o relacionamento de um único registro seja utilizado | Lazy loading pode ser suficiente |
| Um relacionamento será acessado dentro de um laço | Verifique o risco de N + 1 |

O eager loading não deve carregar dados sem necessidade. A escolha depende das informações que serão usadas naquela operação.

> **Exemplo prático:** a pasta [exemplos](exemplos/) contém um
> [BookController](exemplos/BookController.php) com duas actions que executam
> essas estratégias e informam o tempo gasto, além das
> [rotas](exemplos/web.php) que podem ser acrescentadas ao arquivo
> `routes/web.php`. Execute as duas rotas com o banco preenchido e compare os
> resultados. O tempo pode variar entre execuções, mas a diferença tende a
> ficar mais evidente quando aumenta a quantidade de livros.

## 9. Paginação em um Controller

Carregar todos os registros de uma tabela grande consome memória e produz páginas difíceis de utilizar. A paginação divide o resultado em partes menores.

O exemplo seguinte pode ser colocado em:

```text
app/Http/Controllers/BookController.php
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::with(['author', 'categories'])
            ->orderBy('title');

        if ($request->filled('title')) {
            $query->where(
                'title',
                'like',
                '%' . $request->input('title') . '%'
            );
        }

        $books = $query
            ->paginate(10)
            ->withQueryString();

        return view('books.index', compact('books'));
    }
}
```

Esse método:

1. inicia uma consulta de livros;
2. prepara o carregamento de autores e categorias;
3. ordena os livros;
4. aplica o filtro de título quando ele foi enviado;
5. executa a consulta com até dez livros por página;
6. envia o paginador para a View.

`withQueryString()` preserva o filtro nos links das próximas páginas.

Na View `resources/views/books/index.blade.php`:

```blade
@foreach ($books as $book)
    <article>
        <h2>{{ $book->title }}</h2>
        <p>Autor: {{ $book->author?->name ?? 'Não informado' }}</p>

        <ul>
            @foreach ($book->categories as $category)
                <li>{{ $category->name }}</li>
            @endforeach
        </ul>
    </article>
@endforeach

{{ $books->links() }}
```

O método `paginate(10)` identifica a página atual pelo parâmetro `page` da URL e aplica os limites necessários à consulta. `links()` gera os controles de navegação.

Quando a interface precisa apenas dos links “anterior” e “próximo”, sem exibir o total de páginas, também existe `simplePaginate(10)`.

## 10. Cuidados e boas práticas

### Conheça o banco e o SQL

O Eloquent simplifica o acesso aos dados, mas suas operações resultam em consultas ao banco. Conhecer SQL ajuda a:

- entender o resultado das consultas;
- escolher filtros e relacionamentos adequados;
- identificar o problema N + 1;
- compreender índices e restrições;
- investigar problemas de desempenho.

### Evite buscar dados sem necessidade

```php
Book::all();
```

Esse código pode ser adequado para poucos registros. Para listas que podem crescer, prefira filtros e paginação.

### Valide os dados antes de persistir

O ORM não substitui a validação nem a autorização. A aplicação ainda precisa verificar se os valores são válidos e se o usuário pode realizar a operação.

### Use eager loading quando a tela exibir relacionamentos

Se uma lista de livros também exibirá autores e categorias, carregue essas relações na consulta:

```php
Book::with(['author', 'categories'])->paginate(10);
```

### Mantenha as responsabilidades separadas

| Responsabilidade | Local comum |
|---|---|
| Receber a requisição e coordenar a operação | Controller |
| Validar os dados | Form Request ou Controller |
| Representar a entidade e seus relacionamentos | Model |
| Consultar e persistir os Models | Eloquent |
| Definir tabelas, colunas e chaves | Migration |
| Produzir a interface | View |

## 11. Quadro de consulta rápida

| Método | Uso comum | Resultado |
|---|---|---|
| `all()` | Buscar todos os registros | Coleção |
| `find($id)` | Buscar pela chave primária | Model ou `null` |
| `findOrFail($id)` | Buscar um registro obrigatório | Model ou exceção de registro não encontrado |
| `where(...)` | Acrescentar um filtro | Consulta em construção |
| `orderBy(...)` | Ordenar | Consulta em construção |
| `take($n)` | Limitar a quantidade | Consulta em construção |
| `get()` | Executar e buscar vários registros | Coleção |
| `first()` | Executar e buscar o primeiro | Model ou `null` |
| `create([...])` | Inserir um registro | Model criado |
| `save()` | Inserir ou atualizar o objeto | `true` ou `false` |
| `update([...])` | Atualizar atributos autorizados | `true` ou `false` |
| `delete()` | Remover o registro representado | Resultado da remoção |
| `count()` | Contar registros | Número |
| `with(...)` | Preparar eager loading | Consulta em construção |
| `paginate($n)` | Buscar uma página de registros | Paginador |
| `attach()` | Criar associação muitos para muitos | Modifica a pivot |
| `detach()` | Remover associação muitos para muitos | Modifica a pivot |
| `sync()` | Sincronizar associações | Modifica a pivot |

## O que você precisa guardar

1. Um ORM faz o mapeamento entre tabelas relacionais e objetos da aplicação.
2. O Eloquent é o ORM do Laravel e utiliza Models para trabalhar com os dados.
3. A migration define a estrutura; o Eloquent manipula os registros dessa estrutura.
4. Métodos como `where()` e `orderBy()` constroem a consulta; métodos como `get()`, `first()` e `paginate()` a executam.
5. `get()` devolve uma coleção; `first()` e `find()` devolvem um Model ou `null`.
6. `create`, consultas, `update` e `delete` realizam as operações do CRUD.
7. `Fillable` permite preenchimento em conjunto, mas não substitui a validação.
8. Relacionamentos podem ser consultados como propriedades e modificados por meio de seus métodos.
9. Lazy loading busca uma relação quando ela é acessada.
10. Eager loading utiliza `with()` para buscar antecipadamente relações necessárias e evitar o problema N + 1.
11. `paginate()` divide resultados grandes em páginas.
12. O ORM facilita tarefas frequentes, mas não elimina a necessidade de compreender SQL e bancos de dados.

## Referências

- [Laravel 13 - Eloquent: introdução, consultas e persistência](https://laravel.com/docs/13.x/eloquent)
- [Laravel 13 - Relacionamentos do Eloquent](https://laravel.com/docs/13.x/eloquent-relationships)
- [Laravel 13 - Paginação](https://laravel.com/docs/13.x/pagination)
- [Laravel 13 - Migrations](https://laravel.com/docs/13.x/migrations)
- Slides atuais de Web 2 - Aula 04: Laravel Eloquent.
