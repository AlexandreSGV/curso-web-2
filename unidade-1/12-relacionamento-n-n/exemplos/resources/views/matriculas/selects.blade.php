@extends('layouts.app')

@section('title', 'Matricular por listas')

@section('content')
    <form method="POST" action="{{ route('matriculas.store') }}">
        @csrf
        <p>
            <label for="turma_id">Turma</label>
            <select id="turma_id" name="turma_id" required>
                <option value="">Selecione</option>
                @foreach ($turmas as $turma)
                    <option value="{{ $turma->id }}" @selected(old('turma_id') == $turma->id)>{{ $turma->nome }}</option>
                @endforeach
            </select>
        </p>
        <p>
            <label for="aluno_id">Aluno</label>
            <select id="aluno_id" name="aluno_id" required>
                <option value="">Selecione</option>
                @foreach ($alunos as $aluno)
                    <option value="{{ $aluno->id }}" @selected(old('aluno_id') == $aluno->id)>{{ $aluno->nome }}</option>
                @endforeach
            </select>
        </p>
        <button type="submit">Matricular</button>
    </form>
@endsection
