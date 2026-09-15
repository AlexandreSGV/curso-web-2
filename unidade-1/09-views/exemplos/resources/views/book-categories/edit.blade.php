@extends('layouts.app')

@section('title', 'Editar associação com a categoria')

@section('content')
    <p>Livro: <strong>{{ $book->title }}</strong></p>
    <p class="text-sm text-slate-600">Os campos abaixo alteram apenas o vínculo com este livro.</p>
    <form method="POST" action="{{ route('books.categories.update', [$book, $category]) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('book-categories._form')

        <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Salvar vínculo</button>
        <a href="{{ route('books.show', $book) }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
