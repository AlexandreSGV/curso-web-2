<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Book::class);

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
        Gate::authorize('create', Book::class);

        $authors = Author::orderBy('name')->get();

        return view('books.create', compact('authors'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Book::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
            'published_year' => ['nullable', 'integer', 'between:1000,2100'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $book = new Book();
        $this->saveWithCover($book, $validated, $request->file('cover'));

        return redirect()->route('books.show', $book)->with('success', 'Livro cadastrado.');
    }

    public function show(Book $book): View
    {
        Gate::authorize('view', $book);

        $book->load(['author', 'detail']);
        $categories = $book->categories()
            ->orderBy('name')
            ->get();

        return view('books.show', compact('book', 'categories'));
    }

    public function edit(Book $book): View
    {
        Gate::authorize('update', $book);

        $authors = Author::orderBy('name')->get();

        return view('books.edit', compact('book', 'authors'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        Gate::authorize('update', $book);

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
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['sometimes', 'boolean'],
        ]);

        $this->saveWithCover($book, $validated, $request->file('cover'), $request->boolean('remove_cover'));

        return redirect()->route('books.show', $book)->with('success', 'Livro atualizado.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        Gate::authorize('delete', $book);

        $cover = $book->cover;
        $book->delete();
        $this->deleteCover($cover);

        return redirect()->route('books.index')->with('success', 'Livro excluído.');
    }

    private function saveWithCover(
        Book $book,
        array $data,
        ?UploadedFile $image,
        bool $removeCover = false,
    ): void {
        $oldPath = $book->cover;
        $newPath = null;

        if ($image !== null) {
            $newPath = $image->store('covers', 'public');

            if ($newPath === false) {
                throw ValidationException::withMessages([
                    'cover' => 'Não foi possível armazenar a capa.',
                ]);
            }
        }

        // O banco recebe o caminho gerado, não o arquivo nem o valor do checkbox.
        unset($data['cover'], $data['remove_cover']);
        $data['cover'] = $newPath ?? ($removeCover ? null : $oldPath);

        try {
            $book->fill($data)->save();
        } catch (Throwable $error) {
            // Se o banco rejeitar a gravação, descarte apenas o novo arquivo.
            $this->deleteCover($newPath);
            throw $error;
        }

        if ($data['cover'] !== $oldPath) {
            $this->deleteCover($oldPath);
        }
    }

    private function deleteCover(?string $path): void
    {
        if ($path !== null && ! Storage::disk('public')->delete($path)) {
            report(new RuntimeException('Não foi possível excluir a capa: ' . $path));
        }
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
