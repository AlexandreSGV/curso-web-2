@extends('layouts.app')

@section('title', $aluno->exists ? 'Editar aluno' : 'Cadastrar aluno')

@section('content')
    <form method="POST" action="{{ $aluno->exists ? route('alunos.update', $aluno) : route('alunos.store') }}">
        @csrf
        @if ($aluno->exists)
            @method('PUT')
        @endif

        <p>
            <label for="nome">Nome</label>
            <input id="nome" name="nome" value="{{ old('nome', $aluno->nome) }}" required maxlength="255">
        </p>

        <button type="submit">Salvar</button>
        <a href="{{ route('alunos.index') }}">Voltar</a>
    </form>
@endsection
