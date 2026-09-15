@extends('layouts.app')

@section('title', 'Cadastrar livro')

@section('content')
    <form method="POST" action="{{ route('books.store') }}" class="space-y-4">
        @csrf
        @include('books._form')

        <button type="submit" @disabled($authors->isEmpty())
            class="rounded bg-blue-700 px-4 py-2 text-white disabled:opacity-50">Cadastrar</button>
        <a href="{{ route('books.index') }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
