@extends('layouts.app')

@section('title', $turma->exists ? 'Editar turma' : 'Cadastrar turma')

@section('content')
    <form method="POST" action="{{ $turma->exists ? route('turmas.update', $turma) : route('turmas.store') }}">
        @csrf
        @if ($turma->exists)
            @method('PUT')
        @endif

        <p>
            <label for="nome">Nome</label>
            <input id="nome" name="nome" value="{{ old('nome', $turma->nome) }}" required maxlength="255">
        </p>

        <p>
            <label for="professor_id">Professor</label>
            <select id="professor_id" name="professor_id" required>
                <option value="">Selecione</option>
                @foreach ($professores as $professor)
                    <option value="{{ $professor->id }}" @selected(old('professor_id', $turma->professor_id) == $professor->id)>
                        {{ $professor->nome }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Salvar</button>
        <a href="{{ route('turmas.index') }}">Voltar</a>
    </form>
@endsection
