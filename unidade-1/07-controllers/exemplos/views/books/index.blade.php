<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Livros</title>
</head>
<body>
    <h1>Livros</h1>
    <p><a href="{{ route('books.create') }}">Cadastrar livro</a></p>

    <form method="GET" action="{{ route('books.index') }}">
        <label for="title">Título</label>
        <input id="title" name="title" value="{{ request('title') }}" maxlength="255">
        <button type="submit">Buscar</button>
        <a href="{{ route('books.index') }}">Limpar</a>
    </form>
    @error('title')
        <p>{{ $message }}</p>
    @enderror

    @forelse ($books as $book)
        <article>
            <h2><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></h2>
            <p>Autor: {{ $book->author?->name ?? 'Não informado' }}</p>
            <p>Categorias:
                @forelse ($book->categories as $category)
                    {{ $category->name }}{{ $loop->last ? '' : ', ' }}
                @empty
                    Nenhuma categoria.
                @endforelse
            </p>
            <a href="{{ route('books.edit', $book) }}">Editar</a>
        </article>
    @empty
        <p>Nenhum livro encontrado.</p>
    @endforelse

    <nav aria-label="Paginação">
        @if ($books->previousPageUrl())
            <a href="{{ $books->previousPageUrl() }}">Anterior</a>
        @endif
        <span>Página {{ $books->currentPage() }} de {{ $books->lastPage() }}</span>
        @if ($books->hasMorePages())
            <a href="{{ $books->nextPageUrl() }}">Próxima</a>
        @endif
    </nav>
</body>
</html>
