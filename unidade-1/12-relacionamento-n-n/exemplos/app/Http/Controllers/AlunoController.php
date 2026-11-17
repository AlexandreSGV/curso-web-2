<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlunoController extends Controller
{
    public function index(): View
    {
        $alunos = Aluno::orderBy('nome')->get();

        return view('alunos.index', compact('alunos'));
    }

    public function create(): View
    {
        return view('alunos.form', ['aluno' => new Aluno()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $aluno = Aluno::create($dados);

        return redirect()->route('alunos.show', $aluno)->with('success', 'Aluno cadastrado.');
    }

    public function show(Aluno $aluno): View
    {
        $aluno->load('turmas');

        return view('alunos.show', compact('aluno'));
    }

    public function edit(Aluno $aluno): View
    {
        return view('alunos.form', compact('aluno'));
    }

    public function update(Request $request, Aluno $aluno): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $aluno->update($dados);

        return redirect()->route('alunos.show', $aluno)->with('success', 'Aluno atualizado.');
    }

    public function destroy(Aluno $aluno): RedirectResponse
    {
        $aluno->delete();

        return redirect()->route('alunos.index')->with('success', 'Aluno excluído.');
    }
}
