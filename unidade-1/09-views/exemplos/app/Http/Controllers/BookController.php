<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['title' => ['nullable', 'string', 'max:255']]);

        $query = Book::with(['author', 'categories'])->orderBy('title');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        $books = $query->paginate(10)->withQueryString();

        return view('books.index', compact('books'));
    }

    public function create(): View
    {
        $authors = Author::orderBy('name')->get();

        return view('books.create', compact('authors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
            'published_year' => ['nullable', 'integer', 'between:1000,2100'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
        ]);

        $book = Book::create($validated);

        return redirect()->route('books.show', $book)->with('success', 'Livro cadastrado.');
    }

    public function show(Book $book): View
    {
        $book->load(['author', 'detail']);
        $categories = $book->categories()
            ->orderByPivot('position')
            ->orderBy('name')
            ->get();

        return view('books.show', compact('book', 'categories'));
    }

    public function edit(Book $book): View
    {
        $authors = Author::orderBy('name')->get();

        return view('books.edit', compact('book', 'authors'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($book),
            ],
            'published_year' => ['nullable', 'integer', 'between:1000,2100'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
        ]);

        $book->update($validated);

        return redirect()->route('books.show', $book)->with('success', 'Livro atualizado.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livro excluído.');
    }

    // Mantém os exemplos de medição da apostila de Eloquent.
    public function index_lazy_loading(): string
    {
        $inicio = microtime(true);

        $books = Book::all();

        foreach ($books as $book) {
            $book->author?->name;
        }

        $tempo = (microtime(true) - $inicio) * 1000;

        return 'Tempo com lazy loading: '
            . number_format($tempo, 3, ',', '.')
            . ' ms';
    }

    public function index_eager_loading(): string
    {
        $inicio = microtime(true);

        $books = Book::with('author')->get();

        foreach ($books as $book) {
            $book->author?->name;
        }

        $tempo = (microtime(true) - $inicio) * 1000;

        return 'Tempo com eager loading: '
            . number_format($tempo, 3, ',', '.')
            . ' ms';
    }
}
