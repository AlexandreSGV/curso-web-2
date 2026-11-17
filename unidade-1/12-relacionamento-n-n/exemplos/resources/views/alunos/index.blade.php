@extends('layouts.app')

@section('title', 'Alunos')

@section('content')
    <p><a href="{{ route('alunos.create') }}">Cadastrar aluno</a></p>

    <ul>
        @forelse ($alunos as $aluno)
            <li>
                #{{ $aluno->id }} — <a href="{{ route('alunos.show', $aluno) }}">{{ $aluno->nome }}</a>
                · <a href="{{ route('alunos.edit', $aluno) }}">Editar</a>
            </li>
        @empty
            <li>Nenhum registro cadastrado.</li>
        @endforelse
    </ul>
@endsection
