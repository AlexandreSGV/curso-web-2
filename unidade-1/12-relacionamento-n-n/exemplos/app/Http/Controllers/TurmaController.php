<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Professor;
use App\Models\Turma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TurmaController extends Controller
{
    public function index(): View
    {
        $turmas = Turma::with('professor')->orderBy('nome')->get();

        return view('turmas.index', compact('turmas'));
    }

    public function create(): View
    {
        $professores = Professor::orderBy('nome')->get();

        return view('turmas.form', ['turma' => new Turma(), 'professores' => $professores]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'professor_id' => ['required', 'integer', 'exists:professores,id'],
        ]);

        $turma = Turma::create($dados);

        return redirect()->route('turmas.show', $turma)->with('success', 'Turma cadastrada.');
    }

    public function show(Turma $turma): View
    {
        $turma->load(['professor', 'alunos']);
        $alunosDisponiveis = Aluno::whereNotIn('id', $turma->alunos->modelKeys())
            ->orderBy('nome')
            ->get();

        return view('turmas.show', compact('turma', 'alunosDisponiveis'));
    }

    public function edit(Turma $turma): View
    {
        $professores = Professor::orderBy('nome')->get();

        return view('turmas.form', compact('turma', 'professores'));
    }

    public function update(Request $request, Turma $turma): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'professor_id' => ['required', 'integer', 'exists:professores,id'],
        ]);

        $turma->update($dados);

        return redirect()->route('turmas.show', $turma)->with('success', 'Turma atualizada.');
    }

    public function destroy(Turma $turma): RedirectResponse
    {
        $turma->delete();

        return redirect()->route('turmas.index')->with('success', 'Turma excluída.');
    }
}
