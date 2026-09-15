# Views e Blade no Laravel: construindo a interface da biblioteca

Já temos tabelas, Models, dados de exemplo e Controllers. Agora vamos organizar as páginas que permitem ao usuário consultar e modificar esses dados.

Uma **View** cuida da apresentação: títulos, listas, tabelas, mensagens e formulários. Esta apostila utiliza Laravel 13 e continua os exemplos de [Models](../05-models/), [Seeders e Factories](../07-seeders-factories/) e [Controllers](../08-controllers/).

O [exemplo completo](exemplos/) reúne os CRUDs de livros, autores e categorias, além da manutenção dos detalhes de cada livro e de suas associações com categorias. Os trechos da apostila pertencem a esse exemplo; siga o [guia de preparação](exemplos/README.md) para executá-lo.

## Índice

1. [As opções de front-end no Laravel](#1-as-opções-de-front-end-no-laravel)
2. [O que é Blade?](#2-o-que-é-blade)
3. [Enviando dados para uma View](#3-enviando-dados-para-uma-view)
4. [Criando um layout compartilhado](#4-criando-um-layout-compartilhado)
5. [Condições e repetições](#5-condições-e-repetições)
6. [Formulários, reaproveitamento e validação](#6-formulários-reaproveitamento-e-validação)
7. [Views com relacionamentos](#7-views-com-relacionamentos)
8. [Paginação e uso do exemplo](#8-paginação-e-uso-do-exemplo)
9. [Quadro de consulta rápida](#9-quadro-de-consulta-rápida)

## 1. As opções de front-end no Laravel

O Laravel permite construir a interface de diferentes maneiras. Os **starter kits** são pontos de partida com uma estrutura inicial e funcionalidades como autenticação. Eles ajudam a iniciar um projeto, mas não são obrigatórios.

| Opção | Como a interface é construída |
|---|---|
| Blade diretamente | Templates com HTML e expressões PHP, renderizados pelo servidor |
| Starter kit Livewire | Componentes interativos escritos principalmente com PHP e Blade |
| Starter kits React, Vue ou Svelte | Interfaces com JavaScript/TypeScript integradas ao Laravel por meio do Inertia |

O Inertia faz a ligação entre essas interfaces e as rotas e Controllers do Laravel. O kit Livewire utiliza outra abordagem; ele não é um requisito para usar Blade. Consulte os [starter kits oficiais do Laravel 13](https://laravel.com/docs/13.x/starter-kits).

**Nesta etapa usaremos Blade diretamente**, aproveitando HTML, formulários e estruturas de controle já conhecidas. Não é necessário instalar um starter kit para acompanhar o exemplo.

### Front-end em um projeto separado

A interface também pode estar em outro projeto, desenvolvido por outra equipe, usando React, Vue, JavaScript puro ou outra tecnologia, sem Laravel nesse projeto de front-end.

Nesse cenário, o Laravel pode fornecer uma API: recebe requisições, executa as operações e devolve dados, frequentemente em JSON. A aplicação de front-end usa esses dados para montar a interface. As duas partes precisam combinar os endereços, os campos e o formato das respostas.

Na abordagem desta apostila, o próprio Laravel renderiza as páginas HTML com Blade. Essas possibilidades são apresentadas na [documentação de front-end](https://laravel.com/docs/13.x/frontend).

## 2. O que é Blade?

**Blade é o mecanismo de templates do Laravel.** Ele permite combinar HTML com expressões e instruções de apresentação, chamadas de **diretivas**, como `@if` e `@foreach`.

Os arquivos usam a extensão `.blade.php` e ficam em `resources/views`:

| Arquivo | Nome utilizado em `view()` |
|---|---|
| `resources/views/books/index.blade.php` | `books.index` |
| `resources/views/books/show.blade.php` | `books.show` |
| `resources/views/authors/create.blade.php` | `authors.create` |

Blade transforma os templates em PHP, executado **no servidor**. O navegador recebe o HTML resultante; não executa as diretivas Blade. CSS e JavaScript continuam atuando no navegador.

Podemos criar o arquivo manualmente ou, quando ainda não existe, usar:

```bash
php artisan make:view books.index
```

Criar a View não cria uma rota. Uma rota ou um Controller precisa retornar essa View com os dados necessários. Veja a [documentação de Views](https://laravel.com/docs/13.x/views).

## 3. Enviando dados para uma View

O Controller consulta e organiza os dados. A View apresenta o que recebeu.

Um trecho de `BookController::index()`:

```php
$books = Book::with(['author', 'categories'])
    ->orderBy('title')
    ->paginate(10);

return view('books.index', ['books' => $books]);
```

A chave `'books'` torna a variável `$books` disponível na View. `compact('books')`, utilizado no [Controller completo](exemplos/app/Http/Controllers/BookController.php), é outra forma de montar esse array.

No Blade, usamos `{{ ... }}` para apresentar um valor:

```blade
<h2>{{ $book->title }}</h2>
<p>Ano: {{ $book->published_year ?? 'Não informado' }}</p>
```

`??` define um valor alternativo quando o dado é nulo ou não está definido. Não é uma instrução exclusiva de Blade: é sintaxe do PHP.

`{{ ... }}` escapa caracteres especiais do HTML. Assim, um título contendo `<script>` é apresentado como texto. Existe a saída sem escape, `{!! ... !!}`, mas ela não deve ser usada para exibir livremente entradas dos usuários. [Blade — apresentação de dados](https://laravel.com/docs/13.x/blade#displaying-data).

Uma regra para os exemplos: **não faça consultas, inserções ou exclusões dentro da View**. Os relacionamentos utilizados pela tela devem ser preparados no Controller, evitando também consultas inesperadas durante a renderização.

## 4. Criando um layout compartilhado

As telas da biblioteca possuem elementos comuns: cabeçalho, menu, área de conteúdo e rodapé. Repetir tudo em cada arquivo dificulta a manutenção.

Um **layout** funciona como uma folha com cabeçalho pronto e um espaço reservado para o conteúdo. Cada página preenche esse espaço.

Uma versão reduzida de `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Biblioteca')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <header class="bg-slate-900 p-4 text-white">
        <nav class="flex gap-4">
            <a href="{{ route('books.index') }}">Livros</a>
            <a href="{{ route('authors.index') }}">Autores</a>
            <a href="{{ route('categories.index') }}">Categorias</a>
        </nav>
    </header>

    <main class="mx-auto my-6 max-w-5xl rounded bg-white p-6">
        @yield('content')
    </main>
</body>
</html>
```

O [layout completo](exemplos/resources/views/layouts/app.blade.php) acrescenta título da página, mensagens e rodapé. Os nomes das rotas são definidos no [arquivo de rotas](exemplos/routes/web.php).

Uma página utiliza o layout assim:

```blade
@extends('layouts.app')

@section('title', 'Livros')

@section('content')
    <h1 class="text-2xl font-bold">Livros da biblioteca</h1>
    <p>Consulte o acervo ou cadastre um novo livro.</p>
@endsection
```

| Diretiva | Responsabilidade |
|---|---|
| `@extends('layouts.app')` | Indica o layout usado pela página |
| `@yield('content')` | Reserva uma área no layout |
| `@section('content')` | Define o conteúdo que preenche essa área |
| `@endsection` | Encerra a seção com várias linhas |

`@section('title', 'Livros')` define uma seção curta na mesma linha. Os nomes de seção devem corresponder aos nomes utilizados em `@yield`.

O Blade também oferece layouts com componentes e slots. Usaremos a herança de templates para manter a estrutura inicial explícita. [Blade — layouts](https://laravel.com/docs/13.x/blade#layouts-using-template-inheritance).

### O papel do Tailwind

Tailwind oferece classes utilitárias de CSS. Por exemplo, `p-4` acrescenta espaçamento interno, `bg-white` define o fundo branco e `text-xl` altera o tamanho do texto. Essas classes cuidam da aparência; Blade cuida da montagem do HTML.

O script carrega o Tailwind pela internet, por uma CDN, dispensando a compilação de CSS neste exemplo. O [Play CDN](https://tailwindcss.com/docs/installation/play-cdn) é destinado ao desenvolvimento. Para publicar uma aplicação real, prepare o CSS pelo processo de build, normalmente com Vite no Laravel.

## 5. Condições e repetições

### Mostrar conteúdo quando uma condição for atendida

```blade
@if ($book->detail)
    <p>Páginas: {{ $book->detail->pages ?? 'Não informado' }}</p>
@else
    <p>Este livro ainda não possui detalhes.</p>
@endif
```

### Percorrer registros

```blade
@foreach ($authors as $author)
    <p>{{ $author->name }}</p>
@endforeach
```

### Percorrer registros e tratar uma lista vazia

```blade
@forelse ($books as $book)
    <p>{{ $book->title }}</p>
@empty
    <p>Nenhum livro encontrado.</p>
@endforelse
```

`@forelse` combina a repetição com uma resposta para a ausência de registros. Observe seu uso na [listagem de livros](exemplos/resources/views/books/index.blade.php), inclusive para livros sem categorias.

Também podemos escrever comentários que não aparecem no HTML enviado ao navegador:

```blade
{{-- Comentário para quem mantém o template. --}}
```

## 6. Formulários, reaproveitamento e validação

Cadastro e edição de um livro utilizam os mesmos campos. Podemos reuni-los em um **arquivo parcial**, `books/_form.blade.php`, e incluí-lo nas duas páginas.

Um dos campos do [parcial de livros](exemplos/resources/views/books/_form.blade.php):

```blade
<label for="title">Título</label>
<input id="title" name="title"
    value="{{ old('title', $book->title ?? '') }}" required>

@error('title')
    <p class="text-red-700">{{ $message }}</p>
@enderror
```

- `name="title"` é o nome recebido pelo Controller.
- `old('title', ...)` prioriza o valor enviado anteriormente, quando a validação falhou.
- Na primeira abertura da edição, o valor padrão vem de `$book->title`.
- No cadastro, ainda não há `$book`, e o campo começa vazio.
- `@error` apresenta a mensagem de validação daquele campo.

O prefixo `_` é apenas uma convenção para identificar o parcial. `@include('books._form')` incorpora seu conteúdo e disponibiliza as variáveis da View que o chamou.

### Formulário de cadastro

```blade
<form method="POST" action="{{ route('books.store') }}">
    @csrf
    @include('books._form')
    <button type="submit">Cadastrar</button>
</form>
```

### Formulário de edição

```blade
<form method="POST" action="{{ route('books.update', $book) }}">
    @csrf
    @method('PUT')
    @include('books._form')
    <button type="submit">Salvar alterações</button>
</form>
```

`@csrf` inclui o campo de proteção da requisição. Como o formulário HTML envia `POST`, `@method('PUT')` informa ao Laravel que deve tratar a operação como atualização. Para excluir, o exemplo utiliza `@method('DELETE')` em outro formulário.

A validação continua no servidor, usando `$request->validate()`. A View exibe os erros e reapresenta os valores, mas não substitui essa verificação. Depois de salvar, o Controller redireciona para outra página.

O layout também recebe mensagens breves enviadas pelo Controller:

```php
return redirect()->route('books.show', $book)
    ->with('success', 'Livro cadastrado.');
```

```blade
@if (session('success'))
    <p>{{ session('success') }}</p>
@endif
```

Nesse caso, a sessão transporta uma mensagem temporária entre o redirecionamento e a página seguinte. Os registros da biblioteca continuam no banco.

## 7. Views com relacionamentos

Os mesmos Models permitem construir interfaces para os três tipos de relação:

| Relação | O que o usuário faz | Onde encontrar |
|---|---|---|
| Autor 1–N Livro | Escolhe o autor do livro e consulta os livros de um autor | [Formulário de livro](exemplos/resources/views/books/_form.blade.php) e [página do autor](exemplos/resources/views/authors/show.blade.php) |
| Livro 1–1 Detalhes | Cadastra, consulta, edita ou remove páginas e resumo | [Views de detalhes](exemplos/resources/views/book-details/) e [página do livro](exemplos/resources/views/books/show.blade.php) |
| Livro N–N Categoria | Associa categorias e altera os dados de cada vínculo | [Views de associação](exemplos/resources/views/book-categories/) |

### 1–N: selecionar o autor

O Controller fornece `$authors`. O formulário mostra os nomes, mas envia o ID escolhido:

```blade
<select name="author_id" required>
    <option value="">Selecione</option>
    @foreach ($authors as $author)
        <option value="{{ $author->id }}"
            @selected(old('author_id', $book->author_id ?? '') == $author->id)>
            {{ $author->name }}
        </option>
    @endforeach
</select>
```

`@selected` mantém a opção marcada no cadastro com erro ou na edição. O Controller valida a existência do autor antes de gravar `author_id`.

### 1–1: detalhes complementares

A página do livro mostra os detalhes ou o link para cadastrá-los. O [BookDetailController](exemplos/app/Http/Controllers/BookDetailController.php) recebe o livro pela URL e cria o registro pela relação:

```php
$book->detail()->create($validated);
```

O formulário envia apenas `pages` e `summary`. O relacionamento preenche `book_id`; não é preciso pedir ao usuário que digite o ID do livro. A restrição única da migration garante no máximo um detalhe por livro.

Remover os detalhes mantém o livro. A leitura dos detalhes acontece na página do livro, por isso não precisamos de uma listagem separada de `BookDetail`.

### N–N: os dados pertencem à associação

Uma categoria pode estar em destaque para um livro e não para outro. Por isso, `featured` e `position` ficam em `book_category`.

O formulário de associação contém um campo de seleção de categoria, um checkbox e uma posição opcional. O checkbox utiliza:

```blade
<input type="hidden" name="featured" value="0">
<input type="checkbox" name="featured" value="1"
    @checked(old('featured', $category->pivot->featured ?? 0))>
```

Um checkbox desmarcado não envia valor. O campo oculto fornece `0`; quando marcado, o formulário envia também `1`, que é o valor recebido pelo PHP nesse campo. `@checked` recupera a marcação anterior.

No [BookCategoryController](exemplos/app/Http/Controllers/BookCategoryController.php):

| Operação | Uso do Eloquent |
|---|---|
| Associar | `attach($categoryId, $dadosDaPivot)` |
| Editar destaque ou posição | `updateExistingPivot($categoryId, $dadosDaPivot)` |
| Remover o vínculo | `detach($categoryId)` |

Na [página do livro](exemplos/resources/views/books/show.blade.php), `$categories` já foi carregada pelo Controller:

```blade
@foreach ($categories as $category)
    <p>{{ $category->name }}</p>
    <p>Destaque: {{ $category->pivot->featured ? 'Sim' : 'Não' }}</p>
    <p>Posição: {{ $category->pivot->position ?? 'Não informada' }}</p>
@endforeach
```

O exemplo trabalha com a pivot completa da apostila de Seeders e Factories, incluindo os timestamps. **Desassociar uma categoria de um livro não exclui a categoria do sistema.** O CRUD de categorias tem uma ação diferente para essa exclusão.

## 8. Paginação e uso do exemplo

O Controller utiliza `paginate(10)`. Na View, a forma padrão de apresentar os links é:

```blade
{{ $books->links() }}
```

Nos arquivos fornecidos, utilizamos uma [View de paginação simples](exemplos/resources/views/partials/pagination.blade.php), com os textos “Anterior”, “Próxima” e a página atual:

```blade
{{ $books->links('partials.pagination') }}
```

O paginador fornece à View a variável `$paginator`. Na busca por título, `withQueryString()` no Controller mantém o filtro ao trocar de página. [Laravel — paginação](https://laravel.com/docs/13.x/pagination).

### Percurso sugerido para estudar os arquivos

1. Leia [layouts/app.blade.php](exemplos/resources/views/layouts/app.blade.php) e identifique as áreas comuns.
2. Compare [books/create.blade.php](exemplos/resources/views/books/create.blade.php) e [books/edit.blade.php](exemplos/resources/views/books/edit.blade.php): observe rota, método e parcial compartilhado.
3. Acompanhe um cadastro desde o formulário até `BookController::store()` e a página de retorno.
4. Na página do livro, edite os detalhes e associe duas categorias.
5. Altere o destaque de um vínculo e depois desassocie apenas uma categoria.
6. Abra o autor e a categoria para consultar seus livros pelo outro lado das relações.

Os Controllers que faltavam foram incluídos no [exemplo](exemplos/app/Http/Controllers/). O [guia](exemplos/README.md) explica a preparação do banco, os destinos dos arquivos e as rotas. Os Models, migrations e Seeders continuam sendo os da apostila 07.

## 9. Quadro de consulta rápida

| Recurso | Finalidade |
|---|---|
| `{{ $valor }}` | Apresentar um valor com escape de HTML |
| `{{-- ... --}}` | Comentar sem enviar o comentário ao navegador |
| `@extends` | Utilizar um layout |
| `@section` / `@endsection` | Definir uma área de conteúdo |
| `@yield` | Exibir uma seção no layout |
| `@include` | Reaproveitar um arquivo parcial |
| `@if` / `@elseif` / `@else` / `@endif` | Apresentar conteúdo conforme uma condição |
| `@foreach` / `@endforeach` | Percorrer registros |
| `@forelse` / `@empty` / `@endforelse` | Percorrer registros e tratar a lista vazia |
| `@csrf` | Incluir o campo de proteção do formulário |
| `@method` | Representar PUT, PATCH ou DELETE no formulário |
| `@error` / `@enderror` | Exibir erro de um campo |
| `@selected` / `@checked` / `@disabled` | Gerar atributos HTML quando a condição for verdadeira |
| `old()` | Recuperar uma entrada anterior, com valor padrão opcional |
| `route()` | Gerar a URL de uma rota nomeada |

`old()` e `route()` são funções auxiliares do Laravel, não diretivas Blade. Há outros recursos, como componentes, `@auth` e `@can`, para estudar quando forem necessários. Consulte a [documentação completa de Blade](https://laravel.com/docs/13.x/blade).

## O que você precisa guardar

1. Blade monta o HTML no servidor; o navegador recebe o resultado.
2. O Controller prepara os dados, e a View apresenta esses dados.
3. Layouts e parciais evitam repetir a estrutura das páginas e dos formulários.
4. Use saída com escape para apresentar dados dos usuários.
5. Formulários precisam de rota, método e nomes de campos compatíveis com o Controller.
6. `old()` e `@error` ajudam o usuário a corrigir o formulário sem preencher tudo novamente.
7. Views podem apresentar relações 1–1, 1–N e N–N; sua estrutura deve refletir os Models e o banco.
8. Remover um vínculo é uma operação diferente de excluir uma entidade.

## Referências

- [Laravel 13 — Views](https://laravel.com/docs/13.x/views)
- [Laravel 13 — Blade](https://laravel.com/docs/13.x/blade)
- [Laravel 13 — Front-end e starter kits](https://laravel.com/docs/13.x/starter-kits)
- [Laravel 13 — Relacionamentos do Eloquent](https://laravel.com/docs/13.x/eloquent-relationships)
- [Laravel 13 — Paginação](https://laravel.com/docs/13.x/pagination)
- [Tailwind CSS — Play CDN](https://tailwindcss.com/docs/installation/play-cdn)
