@extends('layouts.app')

@section('title', 'Editar autor')

@section('content')
    <form method="POST" action="{{ route('authors.update', $author) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('authors._form')

        <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Salvar alterações</button>
        <a href="{{ route('authors.show', $author) }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
