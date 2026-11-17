<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfessorController extends Controller
{
    public function index(): View
    {
        $professores = Professor::orderBy('nome')->get();

        return view('professores.index', compact('professores'));
    }

    public function create(): View
    {
        return view('professores.form', ['professor' => new Professor()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $professor = Professor::create($dados);

        return redirect()->route('professores.show', $professor)->with('success', 'Professor cadastrado.');
    }

    public function show(Professor $professor): View
    {
        $professor->load('turmas');

        return view('professores.show', compact('professor'));
    }

    public function edit(Professor $professor): View
    {
        return view('professores.form', compact('professor'));
    }

    public function update(Request $request, Professor $professor): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $professor->update($dados);

        return redirect()->route('professores.show', $professor)->with('success', 'Professor atualizado.');
    }

    public function destroy(Professor $professor): RedirectResponse
    {
        if ($professor->turmas()->exists()) {
            return back()->withErrors(['professor' => 'Transfira as turmas para outro professor antes de excluir.']);
        }

        $professor->delete();

        return redirect()->route('professores.index')->with('success', 'Professor excluído.');
    }
}
