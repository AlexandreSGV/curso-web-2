<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar livro</title>
</head>
<body>
    <h1>Editar livro</h1>
    <form method="POST" action="{{ route('books.update', $book) }}">
        @csrf
        @method('PUT')
        @include('books._form')
        <button type="submit">Salvar alterações</button>
    </form>
    <p><a href="{{ route('books.show', $book) }}">Cancelar</a></p>
</body>
</html>
