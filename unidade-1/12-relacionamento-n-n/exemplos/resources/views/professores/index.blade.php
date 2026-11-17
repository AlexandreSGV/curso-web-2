@extends('layouts.app')

@section('title', 'Professores')

@section('content')
    <p><a href="{{ route('professores.create') }}">Cadastrar professor</a></p>

    <ul>
        @forelse ($professores as $professor)
            <li>
                #{{ $professor->id }} — <a href="{{ route('professores.show', $professor) }}">{{ $professor->nome }}</a>
                · <a href="{{ route('professores.edit', $professor) }}">Editar</a>
            </li>
        @empty
            <li>Nenhum registro cadastrado.</li>
        @endforelse
    </ul>
@endsection
