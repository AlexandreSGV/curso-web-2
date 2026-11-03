@extends('layouts.app')

@section('title', 'Editar livro')

@section('content')
    <form method="POST" action="{{ route('books.update', $book) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('books._form')

        <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Salvar alterações</button>
        <a href="{{ route('books.show', $book) }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
