<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Book::class);

        return BookResource::collection(Book::with('author')->orderBy('title')->get());
    }

    public function show(Book $book): BookResource
    {
        Gate::authorize('view', $book);

        return new BookResource($book->load('author'));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Book::class);

        $book = Book::create($this->validateBook($request, new Book()));

        return (new BookResource($book->load('author')))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('api.books.show', $book));
    }

    public function update(Request $request, Book $book): BookResource
    {
        Gate::authorize('update', $book);

        $book->update($this->validateBook($request, $book));

        return new BookResource($book->load('author'));
    }

    public function destroy(Book $book): Response
    {
        Gate::authorize('delete', $book);

        $book->delete();

        return response()->noContent();
    }

    public function authors(): JsonResponse
    {
        Gate::authorize('viewAny', Book::class);

        return response()->json([
            'data' => Author::orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function validateBook(Request $request, Book $book): array
    {
        $isbn = Rule::unique('books', 'isbn');
        if ($book->exists) {
            $isbn->ignore($book);
        }

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'regex:/^[0-9]{13}$/D', $isbn],
            'published_year' => ['present', 'nullable', 'integer', 'between:1000,2100'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
        ]);
    }
}
