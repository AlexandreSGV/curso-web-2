<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $book->title }}</title>
</head>
<body>
    <h1>{{ $book->title }}</h1>
    <p>ISBN: {{ $book->isbn }}</p>
    <p>Ano: {{ $book->published_year ?? 'Não informado' }}</p>
    <p>Autor: {{ $book->author?->name ?? 'Não informado' }}</p>
    <h2>Categorias</h2>
    <ul>
        @forelse ($book->categories as $category)
            <li>{{ $category->name }}</li>
        @empty
            <li>Nenhuma categoria.</li>
        @endforelse
    </ul>
    <p>
        <a href="{{ route('books.edit', $book) }}">Editar</a>
        <a href="{{ route('books.index') }}">Voltar à lista</a>
    </p>
    <form method="POST" action="{{ route('books.destroy', $book) }}">
        @csrf
        @method('DELETE')
        <p>A exclusão é definitiva. Clique somente se quiser remover este livro.</p>
        <button type="submit">Excluir livro</button>
    </form>
</body>
</html>
