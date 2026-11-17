@extends('layouts.app')

@section('title', $professor->exists ? 'Editar professor' : 'Cadastrar professor')

@section('content')
    <form method="POST" action="{{ $professor->exists ? route('professores.update', $professor) : route('professores.store') }}">
        @csrf
        @if ($professor->exists)
            @method('PUT')
        @endif

        <p>
            <label for="nome">Nome</label>
            <input id="nome" name="nome" value="{{ old('nome', $professor->nome) }}" required maxlength="255">
        </p>

        <button type="submit">Salvar</button>
        <a href="{{ route('professores.index') }}">Voltar</a>
    </form>
@endsection
