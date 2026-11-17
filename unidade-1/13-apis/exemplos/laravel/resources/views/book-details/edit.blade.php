@extends('layouts.app')

@section('title', 'Editar detalhes do livro')

@section('content')
    <p>Livro: <strong>{{ $book->title }}</strong></p>
    <form method="POST" action="{{ route('books.detail.update', $book) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('book-details._form')

        <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Salvar detalhes</button>
        <a href="{{ route('books.show', $book) }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
