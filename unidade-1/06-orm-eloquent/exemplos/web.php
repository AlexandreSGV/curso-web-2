<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/books/lazy-loading',
    [BookController::class, 'index_lazy_loading']
);

Route::get(
    '/books/eager-loading',
    [BookController::class, 'index_eager_loading']
);
