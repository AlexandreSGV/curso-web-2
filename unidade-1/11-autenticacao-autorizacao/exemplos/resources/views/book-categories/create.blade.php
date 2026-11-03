@extends('layouts.app')

@section('title', 'Associar categoria ao livro')

@section('content')
    <p>Livro: <strong>{{ $book->title }}</strong></p>
    <form method="POST" action="{{ route('books.categories.store', $book) }}" class="space-y-4">
        @csrf
        @include('book-categories._form')

        <button type="submit" @disabled($categories->isEmpty())
            class="rounded bg-blue-700 px-4 py-2 text-white disabled:opacity-50">Associar</button>
        <a href="{{ route('books.show', $book) }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
