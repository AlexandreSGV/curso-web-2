@extends('layouts.app')

@section('title', 'Entrar')

@section('content')
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <p>
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
        </p>
        <p>
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
        </p>
        <button type="submit">Entrar</button>
    </form>
@endsection
