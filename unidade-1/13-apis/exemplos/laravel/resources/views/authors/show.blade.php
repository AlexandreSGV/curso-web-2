@extends('layouts.app')

@section('title', 'Dados do autor')

@section('content')
    <h2 class="text-xl font-semibold">{{ $author->name }}</h2>
    <a href="{{ route('authors.edit', $author) }}" class="inline-block text-blue-700 underline">Editar autor</a>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold">Livros deste autor</h2>
        <ul class="divide-y divide-slate-200">
            @forelse ($books as $book)
                <li class="py-3"><a href="{{ route('books.show', $book) }}" class="text-blue-700 underline">{{ $book->title }}</a></li>
            @empty
                <li>Nenhum livro cadastrado para este autor.</li>
            @endforelse
        </ul>
        {{ $books->links('partials.pagination') }}
    </section>

    <p class="text-sm text-slate-600">Um autor só pode ser excluído quando não possui livros.</p>
    <form method="POST" action="{{ route('authors.destroy', $author) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded bg-red-700 px-4 py-2 text-white">Excluir autor</button>
    </form>
    <a href="{{ route('authors.index') }}" class="inline-block text-blue-700 underline">Voltar aos autores</a>
@endsection
