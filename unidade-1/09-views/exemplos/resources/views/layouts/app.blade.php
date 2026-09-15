<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Biblioteca') | Web 2</title>
    {{-- Tailwind via CDN para o exemplo local de aula. --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <header class="bg-slate-900 text-white">
        <div class="mx-auto max-w-5xl px-4 py-5">
            <a href="{{ route('books.index') }}" class="text-xl font-bold">Biblioteca</a>
            <nav aria-label="Menu principal" class="mt-3 flex flex-wrap gap-5">
                <a href="{{ route('books.index') }}" class="underline">Livros</a>
                <a href="{{ route('authors.index') }}" class="underline">Autores</a>
                <a href="{{ route('categories.index') }}" class="underline">Categorias</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto my-6 max-w-5xl space-y-6 rounded bg-white p-4 sm:p-6">
        <h1 class="text-2xl font-bold">@yield('title', 'Biblioteca')</h1>

        @if (session('success'))
            <p role="status" class="rounded bg-green-100 p-3 text-green-900">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p role="alert" class="rounded bg-red-100 p-3 text-red-900">{{ session('error') }}</p>
        @endif

        @include('partials.errors')
        @yield('content')
    </main>

    <footer class="mx-auto max-w-5xl px-4 pb-6 text-sm text-slate-600">
        Sistema de biblioteca — Desenvolvimento para Web II
    </footer>
</body>
</html>
