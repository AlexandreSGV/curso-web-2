@extends('layouts.app')

@section('title', 'Cadastrar autor')

@section('content')
    <form method="POST" action="{{ route('authors.store') }}" class="space-y-4">
        @csrf
        @include('authors._form')

        <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Cadastrar</button>
        <a href="{{ route('authors.index') }}" class="ml-3 text-blue-700 underline">Cancelar</a>
    </form>
@endsection
