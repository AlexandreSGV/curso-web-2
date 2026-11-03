@extends('layouts.app')

@section('title', 'Entrar na biblioteca')

@section('content')
    <form method="POST" action="{{ route('login.store') }}" class="max-w-md space-y-4">
        @csrf
        <div class="grid gap-1">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                autocomplete="username" class="rounded border border-slate-300 p-2">
        </div>
        <div class="grid gap-1">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required
                autocomplete="current-password" class="rounded border border-slate-300 p-2">
        </div>
        <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Entrar</button>
    </form>
@endsection
