@extends('layouts.app')

@section('title', 'Dados do livro')

@section('content')
    <section class="space-y-3">
        <h2 class="text-xl font-semibold">{{ $book->title }}</h2>
        <p><strong>ISBN:</strong> {{ $book->isbn }}</p>
        <p><strong>Ano:</strong> {{ $book->published_year ?? 'Não informado' }}</p>
        <p><strong>Autor:</strong>
            @if ($book->author)
                <a href="{{ route('authors.show', $book->author) }}" class="text-blue-700 underline">{{ $book->author->name }}</a>
            @else
                Sem autor
            @endif
        </p>
        <a href="{{ route('books.edit', $book) }}" class="inline-block text-blue-700 underline">Editar livro</a>
    </section>

    <section class="space-y-3 border-t border-slate-200 pt-4">
        <h2 class="text-xl font-semibold">Detalhes complementares</h2>
        @if ($book->detail)
            <p><strong>Páginas:</strong> {{ $book->detail->pages ?? 'Não informado' }}</p>
            <p class="whitespace-pre-line">{{ $book->detail->summary ?? 'Sem resumo.' }}</p>
            <a href="{{ route('books.detail.edit', $book) }}" class="text-blue-700 underline">Editar detalhes</a>
            <form method="POST" action="{{ route('books.detail.destroy', $book) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-700 underline">Remover somente os detalhes</button>
            </form>
        @else
            <p>Este livro ainda não possui detalhes complementares.</p>
            <a href="{{ route('books.detail.create', $book) }}" class="text-blue-700 underline">Cadastrar detalhes</a>
        @endif
    </section>

    <section class="space-y-3 border-t border-slate-200 pt-4">
        <h2 class="text-xl font-semibold">Categorias deste livro</h2>
        <a href="{{ route('books.categories.create', $book) }}" class="inline-block text-blue-700 underline">Associar categoria</a>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-100">
                    <tr>
                        <th scope="col" class="p-3">Categoria</th>
                        <th scope="col" class="p-3">Destaque</th>
                        <th scope="col" class="p-3">Posição</th>
                        <th scope="col" class="p-3">Associação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="p-3"><a href="{{ route('categories.show', $category) }}" class="text-blue-700 underline">{{ $category->name }}</a></td>
                            <td class="p-3">{{ $category->pivot->featured ? 'Sim' : 'Não' }}</td>
                            <td class="p-3">{{ $category->pivot->position ?? 'Não informada' }}</td>
                            <td class="space-y-2 p-3">
                                <a href="{{ route('books.categories.edit', [$book, $category]) }}" class="text-blue-700 underline">Editar vínculo</a>
                                <form method="POST" action="{{ route('books.categories.destroy', [$book, $category]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 underline">Desassociar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3">Nenhuma categoria associada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-sm text-slate-600">Desassociar mantém a categoria cadastrada e os vínculos com outros livros.</p>
    </section>

    <section class="space-y-3 border-t border-slate-200 pt-4">
        <p>Excluir este livro também remove seus detalhes e suas associações. O autor e as categorias são mantidos.</p>
        <form method="POST" action="{{ route('books.destroy', $book) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded bg-red-700 px-4 py-2 text-white">Excluir livro</button>
        </form>
        <a href="{{ route('books.index') }}" class="inline-block text-blue-700 underline">Voltar aos livros</a>
    </section>
@endsection
