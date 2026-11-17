<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookDetailController extends Controller
{
    public function create(Book $book): View|RedirectResponse
    {
        if ($book->detail()->exists()) {
            return redirect()->route('books.detail.edit', $book);
        }

        return view('book-details.create', compact('book'));
    }

    public function store(Request $request, Book $book): RedirectResponse
    {
        if ($book->detail()->exists()) {
            return redirect()->route('books.detail.edit', $book)
                ->with('error', 'Este livro já possui detalhes. Edite o registro existente.');
        }

        $validated = $request->validate([
            'pages' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'summary' => ['nullable', 'string', 'max:5000'],
        ]);

        // O relacionamento preenche book_id com o ID do livro da URL.
        $book->detail()->create($validated);

        return redirect()->route('books.show', $book)->with('success', 'Detalhes cadastrados.');
    }

    public function edit(Book $book): View
    {
        $detail = $book->detail()->firstOrFail();

        return view('book-details.edit', compact('book', 'detail'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $detail = $book->detail()->firstOrFail();
        $validated = $request->validate([
            'pages' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'summary' => ['nullable', 'string', 'max:5000'],
        ]);

        $detail->update($validated);

        return redirect()->route('books.show', $book)->with('success', 'Detalhes atualizados.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $detail = $book->detail()->firstOrFail();
        $detail->delete();

        return redirect()->route('books.show', $book)->with('success', 'Detalhes removidos. O livro foi mantido.');
    }
}
