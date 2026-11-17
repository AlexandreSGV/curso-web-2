<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') | Matrículas</title>
</head>
<body>
    @auth
        <nav aria-label="Menu principal">
            <a href="{{ route('professores.index') }}">Professores</a> |
            <a href="{{ route('turmas.index') }}">Turmas</a> |
            <a href="{{ route('alunos.index') }}">Alunos</a> |
            <a href="{{ route('matriculas.ids') }}">Matricular por IDs</a> |
            <a href="{{ route('matriculas.selects') }}">Matricular por listas</a>
        </nav>
        <p>Usuário: {{ auth()->user()->name }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sair</button>
        </form>
    @endauth

    <main>
        <h1>@yield('title')</h1>

        @if (session('success'))
            <p role="status">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        @endif

        @yield('content')
    </main>
</body>
</html>
