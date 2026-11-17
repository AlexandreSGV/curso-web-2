<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatriculaController extends Controller
{
    public function createPorIds(): View
    {
        return view('matriculas.ids');
    }

    public function createPorSelects(): View
    {
        $turmas = Turma::orderBy('nome')->get();
        $alunos = Aluno::orderBy('nome')->get();

        return view('matriculas.selects', compact('turmas', 'alunos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'turma_id' => ['required', 'integer', 'exists:turmas,id'],
            'aluno_id' => ['required', 'integer', 'exists:alunos,id'],
        ]);

        $turma = Turma::findOrFail($dados['turma_id']);
        $turma->alunos()->syncWithoutDetaching([$dados['aluno_id']]);

        return redirect()->route('turmas.show', $turma)
            ->with('success', 'Matrícula registrada.');
    }

    public function destroy(Turma $turma, Aluno $aluno): RedirectResponse
    {
        $turma->alunos()->detach($aluno->id);

        return redirect()->route('turmas.show', $turma)
            ->with('success', 'Matrícula removida.');
    }
}
