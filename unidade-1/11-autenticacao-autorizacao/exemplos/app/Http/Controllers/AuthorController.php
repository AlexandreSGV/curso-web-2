<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(): View
    {
        $authors = Author::withCount('books')->orderBy('name')->paginate(10);

        return view('authors.index', compact('authors'));
    }

    public function create(): View
    {
        return view('authors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $author = Author::create($validated);

        return redirect()->route('authors.show', $author)->with('success', 'Autor cadastrado.');
    }

    public function show(Author $author): View
    {
        $books = $author->books()->orderBy('title')->paginate(10);

        return view('authors.show', compact('author', 'books'));
    }

    public function edit(Author $author): View
    {
        return view('authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $author->update($validated);

        return redirect()->route('authors.show', $author)->with('success', 'Autor atualizado.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        // A migration impede excluir um autor que ainda possui livros.
        if ($author->books()->exists()) {
            return redirect()->route('authors.show', $author)
                ->with('error', 'Este autor possui livros. Reatribua ou exclua esses livros primeiro.');
        }

        $author->delete();

        return redirect()->route('authors.index')->with('success', 'Autor excluído.');
    }
}
