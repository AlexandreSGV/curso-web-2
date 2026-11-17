@extends('layouts.app')

@section('title', 'Professor')

@section('content')
    <p>#{{ $professor->id }} — {{ $professor->nome }}</p>
    <h2>Turmas do professor</h2>
    <ul>
        @forelse ($professor->turmas as $turma)
            <li><a href="{{ route('turmas.show', $turma) }}">{{ $turma->nome }}</a></li>
        @empty
            <li>Nenhuma turma.</li>
        @endforelse
    </ul>

    <p><a href="{{ route('professores.edit', $professor) }}">Editar professor</a></p>
    <form method="POST" action="{{ route('professores.destroy', $professor) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir professor</button>
    </form>
    <p><a href="{{ route('professores.index') }}">Voltar à lista</a></p>
@endsection
