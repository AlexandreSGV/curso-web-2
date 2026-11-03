<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookDetailController;
use App\Http\Controllers\CategoryController;
use App\Models\Book;
use Illuminate\Support\Facades\Route;

// Rotas da biblioteca com autenticação e autorização.
Route::redirect('/', '/books');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // Administração de autores/categorias e demonstrações de desempenho.
    // As rotas de medição vêm antes da rota /books/{book}.
    Route::middleware('can:manage-catalog')->group(function () {
        Route::get('/books/lazy-loading', [BookController::class, 'index_lazy_loading']);
        Route::get('/books/eager-loading', [BookController::class, 'index_eager_loading']);
        Route::resource('authors', AuthorController::class);
        Route::resource('categories', CategoryController::class);
    });

    // Equivalem às sete rotas resource, com a permissão visível em cada rota.
    Route::get('/books', [BookController::class, 'index'])
        ->can('viewAny', Book::class)->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])
        ->can('create', Book::class)->name('books.create');
    Route::post('/books', [BookController::class, 'store'])
        ->can('create', Book::class)->name('books.store');
    Route::get('/books/{book}', [BookController::class, 'show'])
        ->can('view', 'book')->name('books.show');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])
        ->can('update', 'book')->name('books.edit');
    Route::match(['put', 'patch'], '/books/{book}', [BookController::class, 'update'])
        ->can('update', 'book')->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])
        ->can('delete', 'book')->name('books.destroy');

    // Alterar os detalhes (1–1) e os vínculos (N–N) exige poder editar o livro.
    Route::middleware('can:update,book')->group(function () {
        Route::get('/books/{book}/detail/create', [BookDetailController::class, 'create'])->name('books.detail.create');
        Route::post('/books/{book}/detail', [BookDetailController::class, 'store'])->name('books.detail.store');
        Route::get('/books/{book}/detail/edit', [BookDetailController::class, 'edit'])->name('books.detail.edit');
        Route::put('/books/{book}/detail', [BookDetailController::class, 'update'])->name('books.detail.update');
        Route::delete('/books/{book}/detail', [BookDetailController::class, 'destroy'])->name('books.detail.destroy');

        Route::get('/books/{book}/categories/create', [BookCategoryController::class, 'create'])->name('books.categories.create');
        Route::post('/books/{book}/categories', [BookCategoryController::class, 'store'])->name('books.categories.store');
        Route::get('/books/{book}/categories/{category}/edit', [BookCategoryController::class, 'edit'])->name('books.categories.edit');
        Route::put('/books/{book}/categories/{category}', [BookCategoryController::class, 'update'])->name('books.categories.update');
        Route::delete('/books/{book}/categories/{category}', [BookCategoryController::class, 'destroy'])->name('books.categories.destroy');
    });
});
