# Migrations e models

> Ao final deste assunto, você conseguirá criar a estrutura inicial de `books`, usar o Model `Book` e confirmar no Tinker que um livro foi salvo.

## Antes de começar

Continue na mesma aplicação [`projeto-biblioteca/`](../../projeto-biblioteca/). Não crie outro projeto Laravel.

Se o banco ainda não estiver preparado, consulte a seção **Prepare o banco da biblioteca** no [guia do ambiente Web 2](../../apoio/ambiente-web2-wsl/). Dentro do projeto, confirme no `.env`:

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=biblioteca
DB_USERNAME=biblioteca
```

Use sua senha local em `DB_PASSWORD`, mas não mostre nem envie o `.env`. Inicie o MySQL antes de continuar:

```bash
sudo service mysql start
```

## Três elementos diferentes

Até agora, a página inicial usava rota, controller e Blade, mas não trabalhava com dados da biblioteca. Agora entram três elementos que não devem ser confundidos:

- **tabela:** estrutura real do banco que guarda linhas e colunas;
- **migration:** arquivo PHP que registra uma alteração na estrutura do banco;
- **Model:** classe PHP usada pelo Eloquent para criar e consultar registros.

A migration não guarda livros. Ela descreve como a tabela deve ser criada ou desfeita. O Model não cria a tabela sozinho; ele representa seus registros na aplicação.

## Crie o Model e a migration

O comando usado para gerar os dois arquivos é:

```bash
php artisan make:model Book --migration
```

No projeto de referência, eles já estão disponíveis:

- [`app/Models/Book.php`](../../projeto-biblioteca/app/Models/Book.php);
- [`database/migrations/2026_08_13_000000_create_books_table.php`](../../projeto-biblioteca/database/migrations/2026_08_13_000000_create_books_table.php).

O nome `Book` está no singular. Pela convenção do Laravel, esse Model usa a tabela `books`, no plural. Não precisamos configurar o nome da tabela manualmente.

## Leia a migration de `books`

O método `up` descreve o que será aplicado:

```php
public function up(): void
{
    Schema::create('books', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('isbn', 13)->unique();
        $table->unsignedSmallInteger('published_year')->nullable();
        $table->timestamps();
    });
}
```

As decisões são pequenas e intencionais:

- `title` é obrigatório;
- `isbn` possui 13 caracteres e não pode se repetir;
- `published_year` aceita somente inteiro não negativo e pode ficar vazio quando o ano for desconhecido;
- `timestamps()` cria `created_at` e `updated_at`.

O método `down` descreve a reversão desta mesma mudança:

```php
public function down(): void
{
    Schema::dropIfExists('books');
}
```

Neste caso, aplicar a migration cria `books`; revertê-la remove `books`.

> **Checkpoint:** você consegue apontar qual linha cria a chave primária, qual impede ISBN repetido e qual permite ano vazio.

## Aplique e confirme a estrutura

Confira novamente se o `.env` aponta para o banco de desenvolvimento `biblioteca`. Depois execute:

```bash
php artisan migrate
php artisan migrate:status
```

O status da migration `create_books_table` deve aparecer como **Ran**.

Para observar as colunas diretamente no MySQL:

```bash
mysql -u biblioteca -p biblioteca
```

No prompt `mysql>`, execute:

```sql
SHOW COLUMNS FROM books;
EXIT;
```

O resultado deve incluir `id`, `title`, `isbn`, `published_year`, `created_at` e `updated_at`.

> **Checkpoint:** a tabela existe e possui as seis colunas esperadas.

## Teste uma reversão segura

Faça este teste antes de cadastrar livros. Como `books` é a migration mais recente, reverta somente um passo:

```bash
php artisan migrate:rollback --step=1
php artisan migrate:status
```

O status deve voltar para **Pending**. Aplique novamente:

```bash
php artisan migrate
```

Uma reversão pode remover estrutura e dados. Por isso, primeiro confirme qual banco está configurado e qual migration será afetada.

> **Checkpoint:** após a nova aplicação, `create_books_table` aparece novamente como **Ran**.

## Use o Model `Book`

O Model inicial é curto:

```php
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'isbn', 'published_year'])]
class Book extends Model {}
```

O atributo `Fillable` lista os campos aceitos quando criamos um registro passando um conjunto de valores. Ele aparece porque o próximo exemplo usa `Book::create(...)`. Não incluímos `id` nem timestamps, pois o Laravel cuida desses campos.

Ainda não existem relacionamentos. Eles serão acrescentados somente quando as outras entidades forem estudadas.

## Crie e consulte um livro no Tinker

O Tinker permite interagir com a aplicação pelo terminal. Abra-o:

```bash
php artisan tinker
```

Depois execute:

```php
use App\Models\Book;

$book = Book::create([
    'title' => 'Livro de demonstração',
    'isbn' => '9780000000002',
    'published_year' => 2026,
]);

$book->id;

Book::all(['id', 'title', 'isbn', 'published_year']);
```

O primeiro comando insere uma linha. A consulta seguinte deve devolver uma coleção que contém o livro. Digite `exit` para sair do Tinker.

Agora o Model participa deste fluxo curto:

```text
Tinker → Model Book → tabela books
```

A página web ainda não consulta livros. Rota, controller e Blade serão ligados ao Model em um assunto posterior.

> **Checkpoint final:** você consegue mostrar a migration executada, a tabela criada e o livro retornado pelo Tinker.

## Quando a tabela precisar mudar

Depois que uma migration foi aplicada e compartilhada, preserve seu histórico. Para acrescentar uma coluna, crie outra migration, por exemplo:

```bash
php artisan make:migration add_pages_to_books_table --table=books
```

Esse é o objetivo da [atividade deste assunto](atividade.md). Não edite silenciosamente uma migration que outras pessoas já executaram.

## Comandos que exigem cuidado

`migrate:fresh` apaga todas as tabelas da conexão configurada. `migrate:refresh` reverte e aplica novamente todas as migrations. Esses comandos não pertencem ao fluxo normal deste kit e não devem ser usados para corrigir um erro sem entender o que será apagado.

## Problemas comuns

- `Access denied for user`: confira usuário e senha do MySQL no `.env`;
- `Unknown database 'biblioteca'`: prepare o banco seguindo o guia do ambiente;
- `Table 'biblioteca.books' doesn't exist`: execute `php artisan migrate`;
- erro de ISBN duplicado: use outro ISBN de demonstração; a coluna é única;
- `MassAssignmentException`: confirme o atributo `Fillable` do Model;
- o Tinker não encontra `Book`: confirme o namespace `App\Models` e o comando `use App\Models\Book;`.

## O que você precisa guardar

1. Migration registra uma mudança na estrutura; tabela guarda os dados; Model interage com os registros.
2. `up` aplica a transformação e `down` define como revertê-la.
3. `Book` usa `books` por convenção.
4. Uma nova mudança de estrutura deve ganhar uma nova migration.
5. Comandos de banco devem ser executados somente depois de conferir a conexão.

Próximo assunto: **factories, seeders e Eloquent**.

## Referências

- [Migrations no Laravel 13](https://laravel.com/docs/13.x/migrations)
- [Eloquent no Laravel 13](https://laravel.com/docs/13.x/eloquent)
- [Tinker no Laravel 13](https://laravel.com/docs/13.x/artisan#tinker-repl)
