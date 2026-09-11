<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

// Acrescente ao routes/web.php existente, sem repetir os imports.
// Mantenha rotas específicas, como /books/lazy-loading, ANTES do resource.
// Essas rotas de medição exigem preservar as respectivas actions no Controller.
Route::resource('books', BookController::class);
