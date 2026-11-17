@extends('layouts.app')

@section('title', 'Editar usuário')

@section('content')
    <form method="POST" action="{{ route('users.update', $user) }}" class="max-w-lg space-y-4">
        @csrf
        @method('PUT')

        <div class="grid gap-1">
            <label for="name">Nome</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                class="rounded border border-slate-300 p-2">
        </div>
        <div class="grid gap-1">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255"
                class="rounded border border-slate-300 p-2">
        </div>
        <div class="grid gap-1">
            <label for="role">Papel</label>
            <select id="role" name="role" required class="rounded border border-slate-300 p-2">
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrador</option>
                <option value="bibliotecario" @selected(old('role', $user->role) === 'bibliotecario')>Bibliotecário</option>
                <option value="cliente" @selected(old('role', $user->role) === 'cliente')>Cliente</option>
            </select>
        </div>

        <div class="flex flex-wrap gap-4">
            <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Salvar alterações</button>
            <a href="{{ route('users.show', $user) }}" class="py-2 text-blue-700 underline">Cancelar</a>
        </div>
    </form>
@endsection
