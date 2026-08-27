# Migrations no Laravel: evoluindo a estrutura do banco de dados

As aplicações armazenam informações em bancos de dados. Durante o desenvolvimento, a estrutura desse banco muda: tabelas são criadas, colunas são adicionadas e relacionamentos são ajustados.

No Laravel, essas mudanças podem ser descritas por meio de **migrations**. Elas permitem construir e evoluir a estrutura do banco usando código PHP, de forma organizada e reproduzível.

## Índice

1. [O que são migrations?](#1-o-que-são-migrations)
2. [Migration, tabela e Model](#2-migration-tabela-e-model)
3. [Arquivos, datas e ordem de execução](#3-arquivos-datas-e-ordem-de-execução)
4. [Criando uma migration com Artisan](#4-criando-uma-migration-com-artisan)
5. [Os métodos up e down](#5-os-métodos-up-e-down)
6. [Criando e modificando tabelas](#6-criando-e-modificando-tabelas)
7. [Tipos de colunas mais usados](#7-tipos-de-colunas-mais-usados)
8. [Modificadores, índices e constraints](#8-modificadores-índices-e-constraints)
9. [Alterando uma estrutura existente](#9-alterando-uma-estrutura-existente)
10. [Padrões para nomes de migrations](#10-padrões-para-nomes-de-migrations)
11. [Comandos Artisan relacionados a migrations](#11-comandos-artisan-relacionados-a-migrations)
12. [Fluxo de trabalho recomendado](#12-fluxo-de-trabalho-recomendado)
13. [Para aprofundar](#13-para-aprofundar)
14. [Quadro de consulta rápida](#14-quadro-de-consulta-rápida)

## 1. O que são migrations?

Uma migration é um arquivo PHP que descreve uma alteração na estrutura do banco de dados.

Ela pode, por exemplo:

- criar ou excluir uma tabela;
- adicionar, alterar, renomear ou remover colunas;
- criar índices;
- adicionar ou remover chaves estrangeiras.

Podemos pensar nas migrations como um **histórico versionado da estrutura do banco**. Como esses arquivos ficam junto ao código do projeto, todos os integrantes da equipe podem executar as mesmas alterações em seus próprios bancos.

Sem migrations, seria comum alguém criar uma coluna manualmente e esquecer de avisar aos demais. O código passaria a depender de uma estrutura que não existe nos outros ambientes.

Com migrations, a mudança fica registrada no projeto:

```text
Desenvolvedor cria a migration
        ↓
Arquivo é versionado com o projeto
        ↓
Outros desenvolvedores executam a migration
        ↓
Os bancos recebem a mesma estrutura
```

As migrations controlam principalmente a **estrutura** do banco. Para inserir dados iniciais ou dados de teste, o Laravel oferece seeders e factories, estudados separadamente.

## 2. Migration, tabela e Model

Esses três conceitos se relacionam, mas possuem funções diferentes.

| Elemento | Função |
|---|---|
| Migration | Descreve mudanças na estrutura do banco |
| Tabela | Armazena os registros no banco de dados |
| Model | Representa e manipula os dados da aplicação por meio do Eloquent |

Em um sistema de biblioteca:

- a migration cria a tabela `books`;
- a tabela `books` guarda os livros;
- o Model `Book` permite que a aplicação consulte e altere esses registros.

Uma migration não substitui o Model, e o Model não cria automaticamente toda a estrutura necessária no banco.

## 3. Arquivos, datas e ordem de execução

Por padrão, os arquivos ficam em:

```text
database/migrations
```

Um nome gerado pelo Laravel possui este formato:

```text
2026_08_27_143000_create_books_table.php
```

O início do nome registra **ano, mês, dia, hora, minuto e segundo** da criação:

```text
2026_08_27_143000
```

Essa data e hora funcionam como uma ordem de execução. O Laravel organiza os arquivos pelo nome e executa primeiro as migrations mais antigas.

Isso é importante porque uma alteração pode depender de outra. Antes de adicionar uma coluna à tabela `books`, por exemplo, a tabela precisa ter sido criada.

O horário também reduz a possibilidade de duas migrations receberem exatamente o mesmo nome de arquivo. Mesmo assim, o trecho descritivo deve deixar clara a finalidade da mudança.

> A data e a hora são definidas pelo comando Artisan. Normalmente não é necessário editá-las manualmente.

## 4. Criando uma migration com Artisan

O Artisan é a ferramenta de linha de comando do Laravel. Para criar uma migration para a tabela `books`, execute na raiz do projeto:

```bash
php artisan make:migration create_books_table
```

O Laravel criará um arquivo em `database/migrations`.

Também é possível informar explicitamente que a migration criará uma tabela:

```bash
php artisan make:migration create_books_table --create=books
```

Para criar uma migration que alterará uma tabela existente:

```bash
php artisan make:migration add_isbn_to_books_table --table=books
```

As opções `--create` e `--table` ajudam o Laravel a preparar o código inicial. Em nomes convencionais, ele frequentemente consegue identificar a intenção sem essas opções.

## 5. Os métodos `up` e `down`

Uma migration possui dois métodos principais:

- `up()`: aplica a alteração;
- `down()`: desfaz a alteração.

Exemplo de criação da tabela `books`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
```

Ao executar a migration, o Laravel chama `up()`. Ao desfazê-la com um rollback, chama `down()`.

Os dois métodos devem representar operações opostas:

| `up()` | `down()` |
|---|---|
| Cria uma tabela | Exclui a tabela |
| Adiciona uma coluna | Remove a coluna |
| Renomeia `books` para `publications` | Renomeia `publications` para `books` |
| Adiciona uma constraint | Remove a constraint |

## 6. Criando e modificando tabelas

### Criar uma tabela

Use `Schema::create()`:

```php
Schema::create('books', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->decimal('price', 8, 2);
    $table->boolean('available')->default(true);
    $table->timestamps();
});
```

### Modificar uma tabela

Use `Schema::table()`:

```php
Schema::table('books', function (Blueprint $table) {
    $table->string('isbn')->nullable();
});
```

O método `Schema::table()` não cria uma nova tabela. Ele altera uma tabela que já existe.

### Excluir uma tabela

```php
Schema::dropIfExists('books');
```

### Renomear uma tabela

```php
Schema::rename('books', 'publications');
```

## 7. Tipos de colunas mais usados

O objeto `$table` oferece métodos para representar tipos de dados do banco.

| Método | Uso comum | Exemplo |
|---|---|---|
| `id()` | Chave primária numérica | `$table->id();` |
| `string()` | Textos curtos | `$table->string('title');` |
| `text()` | Textos longos | `$table->text('description');` |
| `integer()` | Números inteiros | `$table->integer('pages');` |
| `bigInteger()` | Inteiros de maior capacidade | `$table->bigInteger('views');` |
| `decimal()` | Valores decimais com precisão definida | `$table->decimal('price', 8, 2);` |
| `boolean()` | Valores verdadeiro ou falso | `$table->boolean('available');` |
| `date()` | Uma data | `$table->date('published_at');` |
| `dateTime()` | Data e horário | `$table->dateTime('reserved_at');` |
| `timestamp()` | Marca de data e horário | `$table->timestamp('confirmed_at');` |
| `timestamps()` | Cria `created_at` e `updated_at` | `$table->timestamps();` |
| `foreignId()` | Coluna usada como chave estrangeira | `$table->foreignId('category_id');` |

O tamanho e o comportamento exatos podem variar entre os bancos compatíveis com o Laravel. Para os casos mais comuns, o Schema Builder faz a tradução para o banco configurado no projeto.

### Escolhendo o tipo

O tipo deve representar o dado que será armazenado:

- nomes, títulos e e-mails: `string()`;
- descrições extensas: `text()`;
- quantidades: `integer()`;
- preços: `decimal()`;
- estados como ativo ou inativo: `boolean()`;
- datas sem horário: `date()`;
- datas com horário: `dateTime()` ou `timestamp()`.

Para dinheiro, prefira `decimal()` a tipos de ponto flutuante, pois ele permite definir a precisão.

## 8. Modificadores, índices e constraints

### Modificadores de coluna

Modificadores complementam a definição de uma coluna.

| Modificador | Finalidade | Exemplo |
|---|---|---|
| `nullable()` | Permite valor nulo | `$table->string('subtitle')->nullable();` |
| `default()` | Define um valor padrão | `$table->boolean('available')->default(true);` |
| `unsigned()` | Impede números negativos | `$table->integer('stock')->unsigned();` |
| `unique()` | Não permite valores repetidos | `$table->string('isbn')->unique();` |
| `index()` | Cria um índice para consultas | `$table->string('title')->index();` |

Os métodos podem ser encadeados:

```php
$table->string('isbn')->nullable()->unique();
```

### Chave primária

O método `id()` cria uma chave primária numérica chamada `id`:

```php
$table->id();
```

### Chave estrangeira

Uma chave estrangeira relaciona registros de tabelas diferentes. O exemplo abaixo relaciona cada livro a uma categoria:

```php
$table->foreignId('category_id')->constrained();
```

Pelas convenções do Laravel, `category_id` será associado à coluna `id` da tabela `categories`.

Também é possível definir o comportamento quando a categoria for excluída:

```php
$table->foreignId('category_id')
    ->constrained()
    ->cascadeOnDelete();
```

Use exclusão em cascata apenas quando a regra da aplicação realmente permitir que a exclusão do registro principal também remova os registros relacionados.

## 9. Alterando uma estrutura existente

Depois que uma migration foi compartilhada ou executada por outras pessoas, a prática mais segura é criar uma **nova migration** para a próxima mudança. Assim, o histórico do projeto permanece consistente.

### Adicionar uma coluna

```php
public function up(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->string('isbn')->nullable();
    });
}

public function down(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->dropColumn('isbn');
    });
}
```

### Remover uma coluna

```php
Schema::table('books', function (Blueprint $table) {
    $table->dropColumn('subtitle');
});
```

Também é possível remover mais de uma coluna:

```php
$table->dropColumn(['subtitle', 'edition']);
```

### Renomear uma coluna

```php
Schema::table('books', function (Blueprint $table) {
    $table->renameColumn('name', 'title');
});
```

### Alterar uma coluna

O método `change()` modifica uma coluna existente:

```php
Schema::table('books', function (Blueprint $table) {
    $table->string('title', 200)->nullable()->change();
});
```

Ao usar `change()`, declare novamente os modificadores que a coluna deve manter, como `nullable()` e `default()`. Modificadores omitidos podem ser removidos da definição.

### Remover uma chave estrangeira

```php
Schema::table('books', function (Blueprint $table) {
    $table->dropForeign(['category_id']);
    $table->dropColumn('category_id');
});
```

Primeiro removemos a constraint e depois a coluna que a utilizava.

## 10. Padrões para nomes de migrations

Os nomes abaixo são **sugestões de padronização**, não uma lista obrigatória imposta pelo Laravel. Uma convenção consistente facilita localizar arquivos, entender o histórico e reconhecer a intenção de cada migration sem precisar abri-la.

Use nomes em inglês, descritivos, em `snake_case` e começando por um verbo.

| Operação | Nome sugerido |
|---|---|
| Criar tabela | `create_books_table` |
| Excluir tabela | `drop_books_table` |
| Renomear tabela | `rename_books_table_to_publications_table` |
| Adicionar coluna | `add_isbn_to_books_table` |
| Adicionar várias colunas | `add_isbn_and_pages_to_books_table` |
| Remover coluna | `drop_subtitle_from_books_table` |
| Renomear coluna | `rename_name_to_title_on_books_table` |
| Adicionar chave estrangeira | `add_category_id_foreign_to_books_table` |
| Remover chave estrangeira | `drop_category_id_foreign_from_books_table` |
| Adicionar índice | `add_isbn_index_to_books_table` |
| Remover índice | `drop_isbn_index_from_books_table` |

Exemplos de comandos:

```bash
php artisan make:migration add_isbn_to_books_table --table=books
php artisan make:migration rename_books_table_to_publications_table
php artisan make:migration drop_category_id_foreign_from_books_table --table=books
```

Uma migration deve representar uma mudança coerente. Não é necessário criar um arquivo separado para cada coluna quando várias colunas pertencem à mesma alteração.

## 11. Comandos Artisan relacionados a migrations

### Criar uma migration

```bash
php artisan make:migration create_books_table
```

### Executar migrations pendentes

```bash
php artisan migrate
```

### Consultar o estado das migrations

```bash
php artisan migrate:status
```

O comando mostra quais migrations já foram executadas e quais ainda estão pendentes.

### Desfazer o último lote

```bash
php artisan migrate:rollback
```

Para desfazer uma quantidade específica de migrations:

```bash
php artisan migrate:rollback --step=1
```

### Executar cada migration em um lote separado

```bash
php artisan migrate --step
```

Isso permite que um rollback posterior desfaça as migrations individualmente.

### Desfazer todas as migrations

```bash
php artisan migrate:reset
```

### Desfazer e executar novamente

```bash
php artisan migrate:refresh
```

Para também executar os seeders:

```bash
php artisan migrate:refresh --seed
```

### Apagar todas as tabelas e reconstruir o banco

```bash
php artisan migrate:fresh
```

Com seeders:

```bash
php artisan migrate:fresh --seed
```

`migrate:fresh` remove todas as tabelas do banco configurado antes de executar as migrations. Ele é útil durante o desenvolvimento, mas apaga os dados existentes.

### Usar um caminho específico

```bash
php artisan migrate --path=database/migrations/modulo-biblioteca
```

### Usar outra conexão configurada

```bash
php artisan migrate --database=mysql
```

## 12. Fluxo de trabalho recomendado

Um fluxo essencial para o desenvolvimento é:

1. defina a mudança necessária na estrutura;
2. crie uma migration com nome descritivo;
3. implemente operações opostas em `up()` e `down()`;
4. execute `php artisan migrate`;
5. confira a estrutura criada e o resultado de `php artisan migrate:status`;
6. teste o rollback antes de compartilhar a mudança;
7. versione a migration junto com o código que depende dela.

Se a migration ainda existe apenas no seu ambiente e contém um erro simples, você pode desfazê-la, corrigi-la e executar novamente. Se ela já foi compartilhada, prefira uma nova migration para corrigir ou evoluir a estrutura.

## 13. Para aprofundar

Depois de dominar a base, a documentação oficial apresenta outros recursos, como:

- índices compostos, `fullText` e índices espaciais;
- diferentes ações para chaves estrangeiras;
- colunas específicas de determinados bancos;
- execução de migrations dentro de transações;
- compactação do histórico com `schema:dump`;
- eventos disparados durante as migrations.

Esses recursos são úteis em projetos maiores, mas não são necessários para o primeiro contato.

## 14. Quadro de consulta rápida

| Necessidade | Código ou comando |
|---|---|
| Criar migration | `php artisan make:migration nome_da_migration` |
| Executar pendentes | `php artisan migrate` |
| Consultar estado | `php artisan migrate:status` |
| Desfazer último lote | `php artisan migrate:rollback` |
| Reconstruir no desenvolvimento | `php artisan migrate:fresh` |
| Criar tabela | `Schema::create('books', ...)` |
| Modificar tabela | `Schema::table('books', ...)` |
| Excluir tabela | `Schema::dropIfExists('books')` |
| Renomear tabela | `Schema::rename('books', 'publications')` |
| Criar chave primária | `$table->id()` |
| Criar datas padrão | `$table->timestamps()` |
| Adicionar coluna opcional | `$table->string('isbn')->nullable()` |
| Adicionar chave estrangeira | `$table->foreignId('category_id')->constrained()` |
| Remover coluna | `$table->dropColumn('isbn')` |

## O que você precisa guardar

1. Migrations registram mudanças na estrutura do banco usando código PHP.
2. Os arquivos ficam normalmente em `database/migrations`.
3. A data e a hora no nome determinam a ordem de execução.
4. O método `up()` aplica uma mudança e `down()` deve desfazê-la.
5. `Schema::create()` cria tabelas e `Schema::table()` altera tabelas existentes.
6. O tipo e os modificadores de uma coluna devem representar corretamente o dado.
7. Mudanças já compartilhadas devem ser feitas, em geral, por novas migrations.
8. `migrate`, `migrate:status` e `migrate:rollback` são os comandos essenciais no dia a dia.
9. `migrate:fresh` reconstrói o banco, mas remove os dados existentes.
10. Nomes consistentes tornam o histórico mais fácil de compreender.

## Referências

- [Laravel — Database: Migrations](https://laravel.com/docs/12.x/migrations)
- [Laravel — Artisan Console](https://laravel.com/docs/12.x/artisan)
- [Laravel — Database: Getting Started](https://laravel.com/docs/12.x/database)
