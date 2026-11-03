<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookCategoryController extends Controller
{
    public function create(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('book-categories.create', compact('book', 'categories'));
    }

    public function store(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'featured' => ['required', 'boolean'],
            'position' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        if ($book->categories()->whereKey($validated['category_id'])->exists()) {
            return back()->withErrors(['category_id' => 'Esta categoria já está associada ao livro.'])
                ->withInput();
        }

        // Os campos extras pertencem ao vínculo entre livro e categoria.
        $book->categories()->attach($validated['category_id'], [
            'featured' => $validated['featured'],
            'position' => $validated['position'] ?? null,
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Categoria associada.');
    }

    public function edit(Book $book, Category $category): View
    {
        // Busca pela relação: uma categoria existente pode não pertencer a este livro.
        $category = $book->categories()->whereKey($category->id)->firstOrFail();

        return view('book-categories.edit', compact('book', 'category'));
    }

    public function update(Request $request, Book $book, Category $category): RedirectResponse
    {
        $category = $book->categories()->whereKey($category->id)->firstOrFail();
        $validated = $request->validate([
            'featured' => ['required', 'boolean'],
            'position' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        $book->categories()->updateExistingPivot($category->id, $validated);

        return redirect()->route('books.show', $book)->with('success', 'Associação atualizada.');
    }

    public function destroy(Book $book, Category $category): RedirectResponse
    {
        $category = $book->categories()->whereKey($category->id)->firstOrFail();
        $book->categories()->detach($category->id);

        return redirect()->route('books.show', $book)->with('success', 'Associação removida. A categoria foi mantida.');
    }
}
