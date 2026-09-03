<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookController extends Controller
{
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
