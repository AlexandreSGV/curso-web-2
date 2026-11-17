<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\TurmaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/turmas');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::resource('professores', ProfessorController::class)->parameters(['professores' => 'professor']);
    Route::resource('alunos', AlunoController::class)->parameters(['alunos' => 'aluno']);
    Route::resource('turmas', TurmaController::class)->parameters(['turmas' => 'turma']);

    Route::get('/matriculas/ids', [MatriculaController::class, 'createPorIds'])->name('matriculas.ids');
    Route::get('/matriculas/selects', [MatriculaController::class, 'createPorSelects'])->name('matriculas.selects');
    Route::post('/matriculas', [MatriculaController::class, 'store'])->name('matriculas.store');
    Route::delete('/turmas/{turma}/matriculas/{aluno}', [MatriculaController::class, 'destroy'])
        ->name('matriculas.destroy');
});
