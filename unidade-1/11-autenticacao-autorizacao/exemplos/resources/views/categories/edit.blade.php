@extends('layouts.app')

@section('title', 'Editar categoria')

@section('content')
    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('categories._form')

        <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Salvar alterações</button>
        <a href="{{ route('categories.show', $category) }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
