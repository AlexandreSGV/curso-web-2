@extends('layouts.app')

@section('title', 'Turma')

@section('content')
    <p>#{{ $turma->id }} — {{ $turma->nome }}</p>
    <p>Professor: <a href="{{ route('professores.show', $turma->professor) }}">{{ $turma->professor->nome }}</a></p>

    <h2>Matricular aluno nesta turma</h2>
    @if ($alunosDisponiveis->isNotEmpty())
        <form method="POST" action="{{ route('matriculas.store') }}">
            @csrf
            <input type="hidden" name="turma_id" value="{{ $turma->id }}">
            <label for="aluno_id">Aluno</label>
            <select id="aluno_id" name="aluno_id" required>
                <option value="">Selecione</option>
                @foreach ($alunosDisponiveis as $aluno)
                    <option value="{{ $aluno->id }}" @selected(old('aluno_id') == $aluno->id)>{{ $aluno->nome }}</option>
                @endforeach
            </select>
            <button type="submit">Matricular</button>
        </form>
    @else
        <p>Nenhum aluno disponível para matrícula.</p>
    @endif

    <h2>Alunos matriculados</h2>
    <ul>
        @forelse ($turma->alunos as $aluno)
            <li>
                <form method="POST" action="{{ route('matriculas.destroy', [$turma, $aluno]) }}">
                    @csrf
                    @method('DELETE')
                    <a href="{{ route('alunos.show', $aluno) }}">{{ $aluno->nome }}</a>
                    <button type="submit" aria-label="Desmatricular {{ $aluno->nome }}">Desmatricular</button>
                </form>
            </li>
        @empty
            <li>Nenhum aluno matriculado.</li>
        @endforelse
    </ul>

    <p><a href="{{ route('turmas.edit', $turma) }}">Editar turma</a></p>
    <form method="POST" action="{{ route('turmas.destroy', $turma) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir turma</button>
    </form>
    <p><a href="{{ route('turmas.index') }}">Voltar à lista</a></p>
@endsection
