# Factories, seeders e Eloquent

> Ao final deste assunto, você conseguirá gerar uma base pequena de livros, consultar os registros com Eloquent e reconhecer quando o resultado é um Model ou uma Collection.

## Antes de começar

Continue na mesma aplicação [`projeto-biblioteca/`](../../projeto-biblioteca/). A migration de `books` e o Model `Book` devem estar prontos.

Inicie o MySQL e confirme que o `.env` aponta para seu banco de desenvolvimento:

```bash
sudo service mysql start
php artisan migrate:status
```

A migration `create_books_table` deve aparecer como executada. Não envie o `.env` ao GitHub.

## Por que preparar dados conhecidos?

Uma consulta é mais fácil de compreender quando sabemos quais resultados devem aparecer. Neste kit, o seeder cria somente seis livros com títulos, ISBNs e anos fixos. Assim, toda a turma pode comparar resultados.

Quatro elementos participam desse processo:

- **Model `Book`:** representa livros e inicia consultas Eloquent;
- **factory:** define valores padrão para gerar um livro;
- **seeder:** escolhe quais dados e quantos registros serão inseridos;
- **registro:** um livro persistido como uma linha da tabela `books`.

A factory não cria uma tabela. O seeder também não substitui a migration. Cada arquivo possui uma responsabilidade diferente.

## Prepare a factory

O comando de geração é:

```bash
php artisan make:factory BookFactory --model=Book
```

O arquivo [`database/factories/BookFactory.php`](../../projeto-biblioteca/database/factories/BookFactory.php) define valores padrão:

```php
public function definition(): array
{
    return [
        'title' => fake()->randomElement([
            'Introdução à Programação Web',
            'Fundamentos de Banco de Dados',
            'Laravel na Prática',
            'Aplicações Web Modernas',
            'Desenvolvimento com PHP',
        ]),
        'isbn' => fake()->unique()->isbn13(),
        'published_year' => fake()->numberBetween(1990, 2026),
    ];
}
```

O Faker produz dados variados. `unique()` evita ISBN repetido durante essa execução. Esses valores são úteis quando precisamos de exemplos diferentes, mas não garantem que uma nova execução gere exatamente os mesmos livros.

Para permitir `Book::factory()`, o Model usa a trait `HasFactory`:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;
}
```

> **Checkpoint:** `php artisan model:show Book` deve mostrar a tabela `books`, sem relacionamentos.

## Registre dados previsíveis no seeder

O [`DatabaseSeeder`](../../projeto-biblioteca/database/seeders/DatabaseSeeder.php) usa a factory, mas substitui seus valores aleatórios por uma sequência fixa:

```php
Book::factory()
    ->count(6)
    ->sequence(
        [
            'title' => 'Fundamentos da Web',
            'isbn' => '9790000000001',
            'published_year' => 2018,
        ],
        // outros cinco livros da sequência
    )
    ->create();
```

O projeto completo contém os seis itens. A lista reduzida acima serve apenas para destacar o formato.

Execute o seeder em um banco de desenvolvimento já migrado:

```bash
php artisan db:seed
```

Depois abra o Tinker:

```bash
php artisan tinker
```

Confira os seis ISBNs reservados para o seeder:

```php
use App\Models\Book;

Book::where('isbn', 'like', '97900000000%')->count();
```

O resultado esperado é `6`. Se existiam outros livros no banco, eles continuam lá e não entram nessa contagem.

> **Checkpoint:** o comando `db:seed` termina sem erro e a consulta retorna seis livros da sequência.

## Recriação exige um banco descartável

Executar o mesmo seeder novamente pode causar erro de ISBN duplicado. Para demonstrar uma recriação completa, use somente um banco criado para ser descartado e confirme seu nome no `.env`.

```bash
# SOMENTE se DB_DATABASE identificar um banco descartável
php artisan migrate:fresh --seed
```

`migrate:fresh` apaga todas as tabelas da conexão configurada. Ele não deve ser usado no banco normal do projeto apenas para corrigir um erro.

## Consulte com Eloquent

Eloquent é o ORM do Laravel. Ele permite trabalhar com registros usando Models e métodos PHP. O SQL continua sendo executado no banco; o ORM oferece outra forma de construir as operações.

Entre no Tinker e importe o Model uma vez:

```php
use App\Models\Book;
```

### Todos os livros

```php
$books = Book::all(['id', 'title', 'isbn', 'published_year']);
```

Ideia equivalente em SQL:

```sql
SELECT id, title, isbn, published_year FROM books;
```

`all()` devolve uma **Collection**: uma lista de Models `Book`. Para observar a lista sem aprender toda a API de Collections agora:

```php
$books->count();
$books->first();
```

### Um livro pela chave

```php
$book = Book::find(1);
```

Isso procura a chave primária `id = 1`. O resultado é um `Book` ou `null`, caso o registro não exista.

```sql
SELECT * FROM books WHERE id = 1 LIMIT 1;
```

### Filtro simples

```php
$recentes = Book::where('published_year', '>=', 2021)
    ->get(['id', 'title', 'published_year']);
```

Em um banco recriado somente com o seeder deste kit, a Collection contém três livros: anos 2021, 2024 e 2026.

### Ordenação

```php
$ordenados = Book::orderBy('published_year', 'desc')
    ->get(['id', 'title', 'published_year']);
```

O primeiro item deve ser **Desenvolvimento Web Moderno**, de 2026, quando o banco contém somente os dados previsíveis.

Filtro e ordenação também podem ser combinados:

```sql
SELECT id, title, published_year
FROM books
WHERE published_year >= 2021
ORDER BY published_year DESC;
```

> **Checkpoint:** você consegue explicar por que `find()` devolve um livro, enquanto `all()` e `get()` devolvem Collections.

### Criação

O Model continua permitindo criar um registro diretamente:

```php
$novoLivro = Book::create([
    'title' => 'Prática com Eloquent',
    'isbn' => '9790000000063',
    'published_year' => 2025,
]);
```

Esse comando corresponde à ideia de um `INSERT`. Ele devolve o Model que acabou de ser salvo. O atributo `Fillable` do Model permite apenas os três campos usados no exemplo.

```php
$novoLivro->id;
Book::find($novoLivro->id);
```

Digite `exit` para sair do Tinker.

> **Checkpoint final:** você consegue gerar os seis livros, filtrar por ano, ordenar e criar mais um registro.

## Problemas comuns

- `Class "Database\\Factories\\BookFactory" not found`: confira namespace, nome do arquivo e execute `composer dump-autoload`;
- `Call to undefined method Book::factory()`: confirme a trait `HasFactory` no Model;
- `Table 'biblioteca.books' doesn't exist`: execute as migrations antes do seeder;
- erro de ISBN duplicado: o seeder já foi executado ou existe um livro com o mesmo ISBN; não use `migrate:fresh` sem confirmar que o banco é descartável;
- `MassAssignmentException` ao usar `Book::create(...)`: confira o atributo `Fillable`;
- `Book::find(...)` devolve `null`: não existe registro com aquela chave;
- resultado diferente da quantidade esperada: o banco pode conter registros de etapas anteriores.

## O que você precisa guardar

1. Factory define valores padrão; seeder coordena a inserção; Model consulta e persiste; registro é um dado salvo.
2. Dados fixos tornam a demonstração comparável e fácil de conferir.
3. `all()` e `get()` devolvem Collections; `find()` devolve um Model ou `null`.
4. `where()` filtra e `orderBy()` ordena antes de `get()` executar a consulta.
5. `migrate:fresh --seed` pertence somente a bancos descartáveis confirmados.

Próximo passo de produção: **publicar os materiais iniciais de Web 2 no Google Slides e Google Forms**.

## Referências

- [Factories no Laravel 13](https://laravel.com/docs/13.x/eloquent-factories)
- [Seeders no Laravel 13](https://laravel.com/docs/13.x/seeding)
- [Eloquent no Laravel 13](https://laravel.com/docs/13.x/eloquent)
