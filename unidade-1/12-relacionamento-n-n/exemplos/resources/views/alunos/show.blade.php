@extends('layouts.app')

@section('title', 'Aluno')

@section('content')
    <p>#{{ $aluno->id }} — {{ $aluno->nome }}</p>
    <h2>Turmas em que está matriculado</h2>
    <ul>
        @forelse ($aluno->turmas as $turma)
            <li><a href="{{ route('turmas.show', $turma) }}">{{ $turma->nome }}</a></li>
        @empty
            <li>Nenhuma turma.</li>
        @endforelse
    </ul>

    <p><a href="{{ route('alunos.edit', $aluno) }}">Editar aluno</a></p>
    <form method="POST" action="{{ route('alunos.destroy', $aluno) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir aluno</button>
    </form>
    <p><a href="{{ route('alunos.index') }}">Voltar à lista</a></p>
@endsection
