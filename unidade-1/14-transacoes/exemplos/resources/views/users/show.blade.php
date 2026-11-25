@extends('layouts.app')

@section('title', 'Dados do usuário')

@section('content')
    <p><strong>ID:</strong> {{ $user->id }}</p>
    <p><strong>Nome:</strong> {{ $user->name }}</p>
    <p><strong>E-mail:</strong> {{ $user->email }}</p>
    <p><strong>Papel:</strong> {{ $user->role }}</p>

    <div class="flex flex-wrap gap-4">
        @can('update', $user)
            <a href="{{ route('users.edit', $user) }}" class="text-blue-700 underline">Editar usuário</a>
        @endcan
        <a href="{{ route('users.index') }}" class="text-blue-700 underline">Voltar aos usuários</a>
    </div>
@endsection
