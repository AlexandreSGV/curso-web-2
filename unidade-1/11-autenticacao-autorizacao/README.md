# Autenticação e autorização no Laravel

O Laravel oferece serviços prontos para identificar usuários e verificar permissões. Nesta apostila, utilizaremos esses recursos para controlar o acesso à biblioteca: quem pode consultar, cadastrar, editar e excluir livros.

O exemplo continua a biblioteca das aulas de [Models](../05-models/), [Seeders e Factories](../07-seeders-factories/), [Controllers](../08-controllers/) e [Views](../09-views/), com Laravel 13 e Blade. Os arquivos complementares estão em [exemplos](exemplos/).

## Índice

1. [O que o Laravel já oferece](#1-o-que-o-laravel-já-oferece)
2. [Login e logout](#2-login-e-logout)
3. [Consultando o usuário autenticado](#3-consultando-o-usuário-autenticado)
4. [Definindo os papéis](#4-definindo-os-papéis)
5. [Criando uma Policy para livros](#5-criando-uma-policy-para-livros)
6. [Consultando a Policy no Controller](#6-consultando-a-policy-no-controller)
7. [Protegendo rotas em web.php](#7-protegendo-rotas-em-webphp)
8. [Adaptando a interface com Blade](#8-adaptando-a-interface-com-blade)
9. [Executando e conferindo o exemplo](#9-executando-e-conferindo-o-exemplo)

## 1. O que o Laravel já oferece

Antes de controlar o acesso, precisamos distinguir dois conceitos:

| Conceito | Pergunta | Exemplo |
|---|---|---|
| **Autenticação** | Quem é o usuário? | Conferir e-mail e senha para entrar |
| **Autorização** | O que esse usuário pode fazer? | Permitir que apenas um administrador exclua livros |

Estar autenticado não significa ter acesso a todas as operações.

O Laravel já fornece:

| Recurso | Finalidade |
|---|---|
| Model `User` e migration de `users` | Representar e armazenar os usuários |
| `Auth` | Autenticar, consultar a identidade atual e encerrar o acesso |
| `Hash` | Gerar e verificar hashes de senhas |
| Middleware `auth` | Exigir um usuário autenticado |
| Middleware `guest` | Destinar uma rota a visitantes sem login |
| Gates | Definir verificações de permissão, como acesso à administração |
| Policies | Reunir permissões relacionadas a um Model, como `Book` |
| Middleware `can` e diretiva `@can` | Consultar permissões em rotas e Views |

**Os mecanismos vêm prontos; as regras da aplicação são definidas por nós.** Os papéis `admin`, `bibliotecario` e `cliente`, por exemplo, não são criados automaticamente pelo Laravel.

Um projeto sem starter kit também não traz as telas de login prontas. Os [starter kits](https://laravel.com/docs/13.x/starter-kits) oferecem essa estrutura e outros fluxos, como recuperação de senha. Aqui acrescentaremos um login simples à biblioteca Blade já construída.

## 2. Login e logout

O usuário informa suas credenciais. O Laravel verifica a senha e, se o login for válido, mantém sua identificação por meio da sessão e de um cookie no navegador.

O `User` gerado pelo Laravel estende `Authenticatable`, uma classe preparada para participar desse processo. As configurações padrão ficam em `config/auth.php`; podemos mantê-las.

Gere o Controller e a View:

```bash
php artisan make:controller AuthController
php artisan make:view auth.login
```

### Receber as credenciais

Em `app/Http/Controllers/AuthController.php`, importe `Request` e `Auth`:

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

O método `store` recebe o formulário:

```php
public function store(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect()->intended(route('books.index'));
    }

    return back()
        ->withErrors(['email' => 'E-mail ou senha inválidos.'])
        ->withInput($request->only('email'));
}
```

`attempt()` procura o usuário e verifica a senha. Envie a senha recebida diretamente a esse método; não aplique `Hash::make()` ao valor do formulário. O hash é usado ao **armazenar** a senha.

`regenerate()` renova o identificador da sessão após o login. `intended()` retorna ao endereço que o usuário tentou acessar; se não houver um, abre a lista de livros. Em caso de falha, preservamos somente o e-mail.

O método que abre a página é:

```php
public function create()
{
    return view('auth.login');
}
```

A [View de login](exemplos/resources/views/auth/login.blade.php) utiliza o layout da biblioteca. Seu formulário envia `email` e `password` para `login.store` e inclui `@csrf`.

### Encerrar o acesso

```php
public function destroy(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}
```

O logout encerra a autenticação, invalida a sessão atual e renova o token CSRF. No layout, o botão **Sair** envia um formulário `POST` para a rota `logout`, também com `@csrf`.

O [AuthController completo](exemplos/app/Http/Controllers/AuthController.php) reúne esses métodos.

## 3. Consultando o usuário autenticado

Dentro de uma action que recebe `Request`, podemos obter o usuário atual:

```php
$user = $request->user();
$name = $user->name;
$email = $user->email;
```

Com a facade `Auth`, as consultas equivalentes são:

```php
$user = Auth::user();
$userId = Auth::id();
$loggedIn = Auth::check();
```

`auth()->user()` é outra forma comum. Sem login, a consulta do usuário retorna `null`; por isso, acesse suas propriedades em rotas protegidas por `auth` ou após conferir a autenticação.

Esse usuário pertence à tabela `users`. O `Author` da biblioteca representa o autor de um livro e não é, automaticamente, uma conta de acesso ao sistema.

## 4. Definindo os papéis

Neste exemplo, cada usuário possui um papel, armazenado na coluna `role` de `users`:

| Operação | Cliente | Bibliotecário | Administrador |
|---|---|---|---|
| Consultar livros, autores e categorias apresentados na página do livro | Sim | Sim | Sim |
| Cadastrar e editar livros | Não | Sim | Sim |
| Manter detalhes (1–1) e associações com categorias (N–N) | Não | Sim | Sim |
| Excluir livros | Não | Não | Sim |
| Acessar os CRUDs de autores e categorias | Não | Não | Sim |

Um papel agrupa responsabilidades. Uma permissão corresponde a uma ação, como `update` ou `delete`. A Policy traduz o papel em permissões.

Crie a migration:

```bash
php artisan make:migration add_role_to_users_table --table=users
```

Nos métodos da migration gerada:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('cliente');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

Preserve os imports de `Schema` e `Blueprint` do arquivo. Depois, execute:

```bash
php artisan migrate
```

Não é necessário declarar uma propriedade `role` no Model `User`: ela é um atributo carregado pelo Eloquent. O valor padrão será `cliente`, inclusive para os usuários já existentes quando a coluna for criada.

O papel é definido por código administrativo, como o [AccessSeeder](exemplos/database/seeders/AccessSeeder.php). Ele usa atribuição explícita:

```php
$user->password = Hash::make('senha-aula-123');
$user->role = 'bibliotecario';
$user->save();
```

Essas linhas ilustram parte do Seeder, que já localizou ou criou o usuário. Mantenha `role` fora do `Fillable` de `User` e não o receba de um formulário público de cadastro ou de edição de perfil. O usuário não deve poder promover a própria conta.

## 5. Criando uma Policy para livros

Uma **Policy (política de autorização)** é uma classe que reúne as regras de autorização de um recurso. Crie a Policy de `Book`:

```bash
php artisan make:policy BookPolicy --model=Book
```

Em `app/Policies/BookPolicy.php`, use:

```php
<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'bibliotecario', 'cliente'], true);
    }

    public function view(User $user, Book $book): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'bibliotecario';
    }

    public function update(User $user, Book $book): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->role === 'admin';
    }
}
```

Cada método retorna `true` para permitir ou `false` para negar. `in_array(..., true)` verifica se o papel corresponde exatamente a um dos valores da lista. Papéis desconhecidos não recebem acesso.

O Laravel fornece o usuário atual ao consultar a Policy. Quando a ação envolve um livro existente, ela também recebe `$book`. Nossa regra depende apenas do papel, mas poderia considerar características daquele registro.

Pelos nomes convencionais `App\Models\Book` e `App\Policies\BookPolicy`, o Laravel descobre a Policy automaticamente. `create` recebe somente o usuário porque o novo livro ainda não existe.

## 6. Consultando a Policy no Controller

Uma Policy só participa da operação quando sua permissão é consultada. No Controller, importe:

```php
use Illuminate\Support\Facades\Gate;
```

Antes de excluir um livro:

```php
public function destroy(Book $book)
{
    Gate::authorize('delete', $book);

    $book->delete();

    return redirect()->route('books.index')->with('success', 'Livro excluído.');
}
```

`Gate::authorize()` encontra `BookPolicy`, chama `delete` com o usuário atual e o livro e interrompe a requisição com **403 — acesso proibido** se a regra negar. A exclusão só acontece após a autorização.

Para uma ação sem registro existente, passe a classe:

```php
Gate::authorize('create', Book::class);
```

O [BookController do exemplo](exemplos/app/Http/Controllers/BookController.php) mantém as validações, consultas e relacionamentos da aula 09 e acrescenta as verificações:

| Actions do Controller | Permissão consultada |
|---|---|
| `index` | `viewAny`, com `Book::class` |
| `show` | `view`, com `$book` |
| `create` e `store` | `create`, com `Book::class` |
| `edit` e `update` | `update`, com `$book` |
| `destroy` | `delete`, com `$book` |

Verifique tanto a action que mostra o formulário quanto a que grava os dados.

Para apenas obter um resultado booleano dentro de uma action autenticada:

```php
$canEdit = $request->user()->can('update', $book);
```

`can()` devolve um resultado para uma decisão do código. `Gate::authorize()` exige a permissão e interrompe o fluxo quando ela falta. A facade também funciona com o Controller base do Laravel 13, sem acrescentar traits.

## 7. Protegendo rotas em web.php

Middleware é um código executado durante o processamento da requisição. Em `routes/web.php`, registre o login e exija autenticação nas páginas internas:

```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Models\Book;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])
    ->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])->name('login.store');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/books/create', [BookController::class, 'create'])
        ->can('create', Book::class)->name('books.create');

    Route::put('/books/{book}', [BookController::class, 'update'])
        ->can('update', 'book')->name('books.update');
});
```

Esse trecho mostra algumas rotas. O [web.php completo](exemplos/routes/web.php) contém todas as operações e substitui as definições equivalentes da aula 09; não duplique as rotas resource.

- `guest` destina o login a visitantes.
- `throttle:5,1` limita o envio do login a cinco requisições por minuto por IP neste exemplo.
- `auth` encaminha o visitante sem login à rota nomeada `login`.
- `can('update', 'book')` consulta a Policy antes da action. `book` é o nome do parâmetro `{book}`, resolvido pelo route model binding.

`auth` verifica a identidade; `can` verifica a permissão. No exemplo, as rotas e o Controller demonstram a consulta à **mesma** Policy. As regras ficam em um único lugar, mesmo quando consultadas em mais de uma camada.

### Permissão geral com Gate

Para o acesso aos CRUDs de autores e categorias, o exemplo define um Gate no `boot()` de `app/Providers/AppServiceProvider.php`:

```php
Gate::define('manage-catalog', function (User $user): bool {
    return $user->role === 'admin';
});
```

Adicione os imports de `App\Models\User` e `Illuminate\Support\Facades\Gate`. Gates são úteis para permissões gerais; Policies organizam as ações de um Model.

Um grupo pode exigir as duas verificações:

```php
Route::middleware(['auth', 'can:manage-catalog'])->group(function () {
    Route::resource('authors', AuthorController::class);
    Route::resource('categories', CategoryController::class);
});
```

Esse trecho pressupõe os imports dos dois Controllers. No arquivo completo, o grupo administrativo já está dentro do grupo `auth`.

As rotas que alteram `BookDetail` e a pivot `book_category` exigem `can:update,book`. Dessa forma, a permissão de editar um livro também controla seus detalhes e associações, preservando as relações 1–1, 1–N e N–N estudadas anteriormente.

## 8. Adaptando a interface com Blade

No layout, podemos identificar o usuário e consultar diretamente seu papel:

```blade
@auth
    <p>Olá, {{ auth()->user()->name }}!</p>
    <p>Papel: {{ auth()->user()->role }}</p>

    @if (auth()->user()->role === 'bibliotecario')
        <p>Área de trabalho do bibliotecário</p>
    @endif
@endauth

@guest
    <a href="{{ route('login') }}">Entrar</a>
@endguest
```

Essa consulta serve para personalizar uma mensagem. Para apresentar ações, prefira consultar a permissão, mantendo a regra na Policy:

```blade
@can('create', \App\Models\Book::class)
    <a href="{{ route('books.create') }}">Cadastrar livro</a>
@endcan

@can('update', $book)
    <a href="{{ route('books.edit', $book) }}">Editar livro</a>
@endcan

@can('delete', $book)
    <form method="POST" action="{{ route('books.destroy', $book) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir livro</button>
    </form>
@endcan
```

O primeiro trecho pode aparecer na listagem. Os trechos com `$book` precisam de uma View que receba esse livro ou de uma repetição que defina a variável.

O menu administrativo utiliza `@can('manage-catalog')`. Os detalhes e vínculos utilizam `@can('update', $book)`, assim como as rotas correspondentes.

**Ocultar o botão adapta a interface, mas não impede uma requisição enviada diretamente. A proteção precisa existir no servidor.** O Blade consulta as permissões no servidor ao montar o HTML; o navegador recebe o resultado.

## 9. Executando e conferindo o exemplo

Siga o [guia de integração](exemplos/README.md) sobre a biblioteca da aula 09. O complemento inclui migration de papel, Seeder de usuários, login, Policy, Controller, rotas e Views adaptadas.

Depois de integrar os arquivos:

```bash
php artisan migrate
php artisan db:seed --class=AccessSeeder
php artisan route:list
php artisan serve
```

Acesse `/login`. As contas de demonstração são `admin@example.com`, `bibliotecario@example.com` e `cliente@example.com`, com a senha local **`senha-aula-123`**.

1. Sem login, tente abrir `/books`: a aplicação deve mostrar o login.
2. Entre como cliente: consulte um livro e confira os relacionamentos; os controles de alteração não devem aparecer.
3. Ainda como cliente, digite `/books/create` na barra de endereços: deve receber 403.
4. Saia e entre como bibliotecário: cadastre e edite um livro, seus detalhes e suas categorias. A exclusão do livro deve permanecer indisponível.
5. Entre como administrador: confira a exclusão e os CRUDs de autores e categorias.
6. Clique em **Sair** e tente abrir `/books` novamente: o login deve ser exigido.

## O que você precisa guardar

1. Autenticação identifica; autorização verifica permissões.
2. O Laravel fornece os mecanismos, e a aplicação define papéis e regras.
3. `Auth::user()` e `$request->user()` recuperam o usuário atual.
4. Policies reúnem permissões de um Model; Gates também permitem verificações gerais.
5. `Gate::authorize()`, `can` nas rotas e `@can` nas Views consultam as regras de autorização.
6. A interface acompanha as permissões, e o servidor garante sua aplicação.

## Referências

- [Laravel 13 — Authentication](https://laravel.com/docs/13.x/authentication)
- [Laravel 13 — Authorization](https://laravel.com/docs/13.x/authorization)
- [Laravel 13 — Starter Kits](https://laravel.com/docs/13.x/starter-kits)
- [Laravel 13 — Hashing](https://laravel.com/docs/13.x/hashing)
- [Laravel 13 — Blade](https://laravel.com/docs/13.x/blade)
