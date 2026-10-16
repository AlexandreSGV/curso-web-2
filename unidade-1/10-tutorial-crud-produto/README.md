# Tutorial: CRUD de produtos no Laravel

Neste tutorial, você construirá um cadastro de produtos no projeto **armazem**, usando Laravel 13, MySQL e Blade. Ao terminar, será possível cadastrar, listar, consultar, editar e excluir produtos.

O exercício reúne os assuntos das aulas anteriores em um exemplo minimalista. Construa o projeto seguindo as etapas e copiando os trechos para os arquivos indicados.

## Etapas

1. [Criar e executar o projeto](#1-criar-e-executar-o-projeto)
2. [Criar a tabela de produtos](#2-criar-a-tabela-de-produtos)
3. [Criar o Model Product](#3-criar-o-model-product)
4. [Criar o Controller e as rotas](#4-criar-o-controller-e-as-rotas)
5. [Cadastrar um produto](#5-cadastrar-um-produto)
6. [Consultar um produto](#6-consultar-um-produto)
7. [Listar os produtos](#7-listar-os-produtos)
8. [Editar um produto](#8-editar-um-produto)
9. [Excluir um produto](#9-excluir-um-produto)

## 1. Criar e executar o projeto

### Preparar o ambiente

Use o [ambiente de desenvolvimento da disciplina](../../apoio/ambiente-web2-wsl/), com PHP 8.3 ou superior, Composer, Node.js/npm, MySQL e VS Code. O PHP precisa ter a extensão `pdo_mysql` habilitada, e o serviço MySQL deve estar em execução.

Se o instalador do Laravel ainda não estiver instalado, execute:

```bash
composer global require laravel/installer
```

No terminal, crie uma pasta de trabalho e entre nela. Você pode substituir `alexandresgv` pelo seu nome:

```bash
mkdir alexandresgv
cd alexandresgv
```

Crie o projeto:

```bash
laravel new armazem
```

Nas perguntas do instalador, escolha:

- **Starter kit:** No / None (sem kit), conforme a versão do instalador;
- **Frontend stack**, se essa pergunta aparecer: Blade;
- **Banco de dados:** MySQL;
- **Executar as migrations agora:** No, pois primeiro configuraremos a conexão.

Mantenha as demais opções padrão.

Entre na pasta e abra o projeto no VS Code:

```bash
cd armazem
code .
```

A partir daqui, execute os comandos do Laravel na pasta `armazem`, onde está o arquivo `artisan`.

### Preparar a conexão com o banco

Este roteiro considera o MySQL local na porta **3308**, com usuário e senha `root`. Ajuste esses dados se o seu ambiente utilizar outros valores.

Conecte-se pelo terminal:

```bash
mysql -h 127.0.0.1 -P 3308 -u root -p
```

Quando a senha for solicitada, digite `root`. No prompt do MySQL, crie o banco e saia:

```sql
CREATE DATABASE IF NOT EXISTS armazem;
EXIT;
```

Abra o arquivo `.env`, na raiz do projeto, e configure apenas os dados da conexão:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=armazem
DB_USERNAME=root
DB_PASSWORD=root
```

Mantenha as demais configurações padrão do Laravel. Salve o arquivo e execute:

```bash
php artisan migrate
```

Esse comando cria as tabelas das migrations iniciais do Laravel. A tabela de produtos será criada na próxima etapa.

### Iniciar a aplicação

Instale e prepare as dependências da parte visual:

```bash
npm install
npm run build
```

Se o instalador já executou esses dois comandos com sucesso, não é necessário repeti-los.

Inicie o ambiente de desenvolvimento:

```bash
composer run dev
```

Abra [http://localhost:8000](http://localhost:8000) e confira a página inicial do Laravel. Use o endereço informado no terminal se a porta for diferente.

**Deixe esse terminal aberto.** Para os próximos comandos, abra um segundo terminal no VS Code, também na pasta `armazem`. Para encerrar o servidor ao fim da atividade, use `Ctrl+C` no primeiro terminal.

## 2. Criar a tabela de produtos

Crie uma migration para a tabela `products`:

```bash
php artisan make:migration create_products_table
```

Abra o arquivo gerado em `database/migrations/`. Seu nome começa com a data e a hora e termina com `_create_products_table.php`.

Substitua o conteúdo pelo código abaixo:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->date('expiration_date');
            $table->float('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

| Coluna | Informação armazenada |
|---|---|
| `id` | Identificador gerado pelo banco |
| `name` | Nome do produto |
| `description` | Descrição |
| `expiration_date` | Data de validade |
| `value` | Valor numérico |
| `created_at` e `updated_at` | Datas de criação e atualização, geradas por `timestamps()` |

Salve e execute a migration:

```bash
php artisan migrate
```

A migration descreve a estrutura; o comando aplica essa estrutura no banco.

Para conferir o resultado, conecte-se novamente ao MySQL:

```bash
mysql -h 127.0.0.1 -P 3308 -u root -p
```

No prompt do MySQL, execute:

```sql
SHOW DATABASES;
USE armazem;
SHOW TABLES;
DESC products;
EXIT;
```

Confira se `products` aparece e se as colunas correspondem à migration. `EXIT` retorna ao terminal para continuar o tutorial.

## 3. Criar o Model Product

Crie o Model:

```bash
php artisan make:model Product
```

Abra **`app/Models/Product.php`** e deixe o arquivo assim:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'expiration_date', 'value'])]
class Product extends Model
{
}
```

Pela convenção do Eloquent, `Product`, no singular, utiliza a tabela `products`, no plural.

O atributo `Fillable` indica quais campos podem ser preenchidos em conjunto por operações como `create()` e `update()`. O `id` e os timestamps são controlados automaticamente. Essa configuração não valida o conteúdo dos campos.

## 4. Criar o Controller e as rotas

Gere o Controller do tipo resource, associado ao Model `Product`:

```bash
php artisan make:controller ProductController --model=Product --resource
```

O arquivo será criado em `app/Http/Controllers/ProductController.php`, com os sete métodos que implementaremos ao longo do tutorial.

Confira se os imports abaixo estão no início do arquivo, após o `namespace`. O comando já deve tê-los incluído; não os duplique:

```php
use App\Models\Product;
use Illuminate\Http\Request;
```

**Nos próximos passos, substitua os métodos já gerados dentro da classe `ProductController`.** Preserve o restante do arquivo e evite criar dois métodos com o mesmo nome.

Abra `routes/web.php`. Acrescente o import de `ProductController` junto aos demais imports e registre a rota resource abaixo deles:

```php
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);
```

Mantenha o import de `Route` que já existe nesse arquivo. Uma única chamada a `Route::resource()` registra as rotas abaixo:

| Método HTTP | Endereço | Action | Nome da rota |
|---|---|---|---|
| GET | `/products` | `index` | `products.index` |
| GET | `/products/create` | `create` | `products.create` |
| POST | `/products` | `store` | `products.store` |
| GET | `/products/{product}` | `show` | `products.show` |
| GET | `/products/{product}/edit` | `edit` | `products.edit` |
| PUT/PATCH | `/products/{product}` | `update` | `products.update` |
| DELETE | `/products/{product}` | `destroy` | `products.destroy` |

Não registre essas rotas novamente de forma individual. Para conferir as definições, execute:

```bash
php artisan route:list --path=products
```

As rotas estarão registradas, mas cada operação só funcionará depois que implementarmos a action e sua View.

## 5. Cadastrar um produto

### Criar o formulário

Gere a View de cadastro:

```bash
php artisan make:view products.create
```

Abra `resources/views/products/create.blade.php` e substitua seu conteúdo por:

```blade
<h1>Cadastrar Produto</h1>
<form method="POST" action="{{ route('products.store') }}">
    @csrf

    Name:
    <input name="name"> <br>
    Description:
    <input name="description"> <br>
    Expiration Date:
    <input type="date" name="expiration_date"> <br>
    Value:
    <input name="value"> <br>

    <button type="submit">Cadastrar Produto</button>
</form>
```

Os atributos `name` dos campos correspondem às colunas da tabela. O formulário envia uma requisição `POST` para a rota `products.store`. A diretiva `@csrf` inclui o token de proteção esperado pelo Laravel.

### Exibir e processar o formulário

No `ProductController`, substitua a action `create`:

```php
public function create()
{
    return view('products.create');
}
```

`view('products.create')` procura o arquivo `resources/views/products/create.blade.php`.

Substitua também a action `store`:

```php
public function store(Request $request)
{
    $product = Product::create($request->all());
    return view('products.create');
}
```

O objeto `$request` contém os dados da requisição. `Product::create()` insere um registro usando os campos permitidos em `Fillable` e devolve o produto criado.

Abra [http://localhost:8000/products/create](http://localhost:8000/products/create), preencha todos os campos e cadastre um produto. Para o valor, use ponto como separador decimal, por exemplo `12.50`. Para a validade, selecione uma data no campo.

Por enquanto, após salvar, a aplicação mostra o formulário novamente. Na etapa 7, trocaremos essa resposta por um redirecionamento para a listagem.

### Conferir o cadastro no banco

No segundo terminal, conecte-se ao MySQL:

```bash
mysql -h 127.0.0.1 -P 3308 -u root -p
```

Execute:

```sql
USE armazem;
SELECT * FROM products;
EXIT;
```

Confira os dados e anote o `id` do produto. Ele será usado nos próximos testes.

## 6. Consultar um produto

No `ProductController`, substitua a action `show`:

```php
public function show(Product $product)
{
    return view('products.show', compact('product'));
}
```

O parâmetro `Product $product` utiliza o **route model binding**: o Laravel busca o produto cujo `id` veio na URL. Se ele não existir, a resposta será 404.

`compact('product')` envia a variável `$product` para a View com esse mesmo nome.

Gere a View:

```bash
php artisan make:view products.show
```

Em `resources/views/products/show.blade.php`, coloque:

```blade
Name: {{ $product->name }} <br>
Description: {{ $product->description }} <br>
Expiration Date: {{ $product->expiration_date }} <br>
Value: {{ $product->value }} <br>
```

As expressões `{{ }}` apresentam os valores do produto com escape de HTML.

Acesse `http://localhost:8000/products/1`, substituindo `1` pelo `id` cadastrado. Confira se a página apresenta os dados daquele registro.

## 7. Listar os produtos

No `ProductController`, substitua a action `index`:

```php
public function index()
{
    $products = Product::all();
    return view('products.index', compact('products'));
}
```

`Product::all()` consulta os produtos e retorna uma coleção. Ela será percorrida pela View.

Gere a página de listagem:

```bash
php artisan make:view products.index
```

Em `resources/views/products/index.blade.php`, coloque:

```blade
@foreach ($products as $product)
    {{ $product->id }} - {{ $product->name }} - {{ $product->expiration_date }} - {{ $product->value }} <br>
@endforeach
```

`@foreach` repete o trecho para cada produto. Com a tabela vazia, nenhuma linha é apresentada.

Abra [http://localhost:8000/products](http://localhost:8000/products) e confira a listagem.

Agora que essa página existe, substitua novamente a action `store`:

```php
public function store(Request $request)
{
    Product::create($request->all());
    return redirect()->route('products.index');
}
```

O cadastro continua usando `create()`. A diferença é a resposta: `redirect()->route()` orienta o navegador a fazer uma nova requisição para a listagem. Assim, atualizar a listagem não reenvia o formulário de cadastro.

Volte a `/products/create` e cadastre outro produto. A aplicação deve abrir a lista com o novo registro.

## 8. Editar um produto

### Abrir o formulário preenchido

No `ProductController`, substitua a action `edit`:

```php
public function edit(Product $product)
{
    return view('products.edit', compact('product'));
}
```

Assim como em `show`, o Laravel localiza o produto pelo identificador da URL e o envia à View.

Crie a View:

```bash
php artisan make:view products.edit
```

Em `resources/views/products/edit.blade.php`, coloque:

```blade
<h1>Editar Produto</h1>
<form method="POST" action="{{ route('products.update', $product) }}">
    @csrf
    @method('PUT')

    Name:
    <input name="name" value="{{ $product->name }}"> <br>
    Description:
    <input name="description" value="{{ $product->description }}"> <br>
    Expiration Date:
    <input type="date" name="expiration_date" value="{{ $product->expiration_date }}"> <br>
    Value:
    <input name="value" value="{{ $product->value }}"> <br>

    <button type="submit">Editar Produto</button>
</form>
```

O atributo `value` preenche cada campo com seu valor atual. `route('products.update', $product)` gera o endereço de atualização daquele produto.

O formulário HTML usa `POST`; `@method('PUT')` acrescenta um campo que faz o Laravel tratar a requisição como `PUT`, encaminhando-a para `update`.

### Salvar as alterações

Substitua a action `update`:

```php
public function update(Request $request, Product $product)
{
    $product->update($request->all());
    return redirect()->route('products.show', $product->id);
}
```

`$product` representa o registro existente. `update()` altera seus campos e, em seguida, o navegador é redirecionado para a página de detalhes.

Acesse `http://localhost:8000/products/1/edit`, usando um `id` existente. Altere algum valor, envie o formulário e confira a mudança na página de detalhes.

## 9. Excluir um produto

No `ProductController`, substitua a action `destroy`:

```php
public function destroy(Product $product)
{
    $product->delete();
    return redirect()->route('products.index');
}
```

`delete()` remove o registro do banco. Depois, a aplicação retorna à listagem.

Para acessar as operações pela própria lista, substitua o conteúdo de `resources/views/products/index.blade.php` por:

```blade
@foreach ($products as $product)
    {{ $product->id }} -
    {{ $product->name }} -
    {{ $product->expiration_date }} -
    {{ $product->value }} -
    <a href="{{ route('products.edit', $product) }}">Editar</a> -
    <a href="{{ route('products.show', $product) }}">Detalhes</a>
    <form method="POST" action="{{ route('products.destroy', $product) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir</button>
    </form>
@endforeach
```

Os links **Editar** e **Detalhes** fazem requisições `GET`. A exclusão utiliza um formulário com `@csrf` e `@method('DELETE')`, que aciona `destroy`.

Abra `/products`, escolha um produto de teste e clique em **Excluir**. Confira se ele desapareceu da listagem.

### Conferência final

Percorra as quatro operações do CRUD:

| Operação | Como testar |
|---|---|
| Criar | Acesse `/products/create`, preencha todos os campos e cadastre |
| Consultar | Abra `/products` e clique em **Detalhes** |
| Atualizar | Clique em **Editar**, altere um campo e salve |
| Excluir | Volte à lista, clique em **Excluir** e confira a remoção |

O projeto agora reúne migration, Model, Controller, rotas e quatro Views. Os métodos do Controller coordenam as operações; o Eloquent manipula os registros; o Blade apresenta os dados e os formulários.

## Correções pontuais em relação ao roteiro original

- Corrigido o caminho do Model para `app/Models/Product.php` e explicitados os imports necessários.
- Incluída a criação do banco `armazem` antes das migrations.
- Ajustados os comandos do cliente MySQL para usar o mesmo host e a porta `3308` da conexão do Laravel; os comandos SQL foram separados dos comandos do terminal.
- Incluída a orientação para adiar as migrations do instalador até configurar a conexão.
- Incluídos os comandos `npm install` e `npm run build`, os geradores das quatro Views e a conferência de rotas.
- Corrigida a representação do parâmetro das rotas para `{product}`, compatível com `Product $product`.

## Referências

- [Laravel 13 — Instalação](https://laravel.com/docs/13.x/installation)
- [Laravel 13 — Migrations](https://laravel.com/docs/13.x/migrations)
- [Laravel 13 — Eloquent](https://laravel.com/docs/13.x/eloquent)
- [Laravel 13 — Controllers resource](https://laravel.com/docs/13.x/controllers#resource-controllers)
- [Laravel 13 — Views](https://laravel.com/docs/13.x/views)
- [Laravel 13 — Blade](https://laravel.com/docs/13.x/blade)
