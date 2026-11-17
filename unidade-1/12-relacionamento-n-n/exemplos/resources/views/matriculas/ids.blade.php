@extends('layouts.app')

@section('title', 'Matricular por IDs')

@section('content')
    <p>Consulte os IDs nas listas de turmas e alunos.</p>
    <form method="POST" action="{{ route('matriculas.store') }}">
        @csrf
        <p>
            <label for="turma_id">ID da turma</label>
            <input id="turma_id" type="number" name="turma_id" min="1" value="{{ old('turma_id') }}" required>
        </p>
        <p>
            <label for="aluno_id">ID do aluno</label>
            <input id="aluno_id" type="number" name="aluno_id" min="1" value="{{ old('aluno_id') }}" required>
        </p>
        <button type="submit">Matricular</button>
    </form>
@endsection
