<?php

require __DIR__ . '/model.php';

$mensagem = '';
$erro = '';
$titulo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');

    if ($titulo === '') {
        $erro = 'Informe o título do livro.';
    } else {
        cadastrarLivro($titulo);
        $mensagem = 'Livro cadastrado com sucesso.';
        $titulo = '';
    }
}

require __DIR__ . '/view.php';
