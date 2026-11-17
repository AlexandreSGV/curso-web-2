<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('/tokens', [TokenController::class, 'store'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/tokens/current', [TokenController::class, 'destroy']);
        Route::get('/authors', [BookController::class, 'authors']);

        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });
});
