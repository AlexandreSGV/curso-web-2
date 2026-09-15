<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookDetailController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Integre ao web.php existente, sem duplicar imports ou rotas do mesmo CRUD.
// As duas rotas da apostila de Eloquent ficam antes de /books/{book}.
Route::get('/books/lazy-loading', [BookController::class, 'index_lazy_loading']);
Route::get('/books/eager-loading', [BookController::class, 'index_eager_loading']);

Route::resource('authors', AuthorController::class);
Route::resource('categories', CategoryController::class);
Route::resource('books', BookController::class);

// Relação 1–1: os detalhes são acessados pelo livro, sem um segundo ID na URL.
Route::get('/books/{book}/detail/create', [BookDetailController::class, 'create'])->name('books.detail.create');
Route::post('/books/{book}/detail', [BookDetailController::class, 'store'])->name('books.detail.store');
Route::get('/books/{book}/detail/edit', [BookDetailController::class, 'edit'])->name('books.detail.edit');
Route::put('/books/{book}/detail', [BookDetailController::class, 'update'])->name('books.detail.update');
Route::delete('/books/{book}/detail', [BookDetailController::class, 'destroy'])->name('books.detail.destroy');

// Relação N–N: cada associação pode ser criada, editada e removida separadamente.
Route::get('/books/{book}/categories/create', [BookCategoryController::class, 'create'])->name('books.categories.create');
Route::post('/books/{book}/categories', [BookCategoryController::class, 'store'])->name('books.categories.store');
Route::get('/books/{book}/categories/{category}/edit', [BookCategoryController::class, 'edit'])->name('books.categories.edit');
Route::put('/books/{book}/categories/{category}', [BookCategoryController::class, 'update'])->name('books.categories.update');
Route::delete('/books/{book}/categories/{category}', [BookCategoryController::class, 'destroy'])->name('books.categories.destroy');
