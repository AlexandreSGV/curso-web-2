@extends('layouts.app')

@section('title', 'Dados da categoria')

@section('content')
    <h2 class="text-xl font-semibold">{{ $category->name }}</h2>
    <a href="{{ route('categories.edit', $category) }}" class="inline-block text-blue-700 underline">Editar categoria</a>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold">Livros desta categoria</h2>
        <ul class="divide-y divide-slate-200">
            @forelse ($books as $book)
                <li class="space-y-1 py-3">
                    <a href="{{ route('books.show', $book) }}" class="text-blue-700 underline">{{ $book->title }}</a>
                    <p>Autor: {{ $book->author?->name ?? 'Sem autor' }}</p>
                    <p class="text-sm">Destaque: {{ $book->pivot->featured ? 'Sim' : 'Não' }}.
                        Posição: {{ $book->pivot->position ?? 'Não informada' }}.</p>
                </li>
            @empty
                <li>Nenhum livro associado a esta categoria.</li>
            @endforelse
        </ul>
        {{ $books->links('partials.pagination') }}
    </section>

    <p>Excluir esta categoria remove suas associações com todos os livros. Os livros são mantidos.</p>
    <form method="POST" action="{{ route('categories.destroy', $category) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded bg-red-700 px-4 py-2 text-white">Excluir categoria</button>
    </form>
    <a href="{{ route('categories.index') }}" class="inline-block text-blue-700 underline">Voltar às categorias</a>
@endsection
