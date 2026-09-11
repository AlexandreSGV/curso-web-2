<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar livro</title>
</head>
<body>
    <h1>Cadastrar livro</h1>
    <form method="POST" action="{{ route('books.store') }}">
        @csrf
        @include('books._form')
        <button type="submit">Cadastrar</button>
    </form>
    <p><a href="{{ route('books.index') }}">Voltar</a></p>
</body>
</html>
