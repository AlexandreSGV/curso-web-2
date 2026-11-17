@extends('layouts.app')

@section('title', 'Turmas')

@section('content')
    <p><a href="{{ route('turmas.create') }}">Cadastrar turma</a></p>

    <ul>
        @forelse ($turmas as $turma)
            <li>
                #{{ $turma->id }} — <a href="{{ route('turmas.show', $turma) }}">{{ $turma->nome }}</a> — {{ $turma->professor->nome }}
                · <a href="{{ route('turmas.edit', $turma) }}">Editar</a>
            </li>
        @empty
            <li>Nenhum registro cadastrado.</li>
        @endforelse
    </ul>
@endsection
