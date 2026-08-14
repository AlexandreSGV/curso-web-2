<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class InicioController extends Controller
{
    public function index(): View
    {
        return view('biblioteca.inicio', [
            'titulo' => 'Biblioteca Web 2',
            'mensagem' => 'A aplicação Laravel está funcionando.',
        ]);
    }
}
