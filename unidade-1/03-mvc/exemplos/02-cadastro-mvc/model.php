<?php

function cadastrarLivro($titulo)
{
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=biblioteca;charset=utf8mb4',
        'biblioteca',
        getenv('DB_PASSWORD') ?: ''
    );

    $comando = $pdo->prepare(
        'INSERT INTO livros (titulo) VALUES (:titulo)'
    );
    $comando->execute(['titulo' => $titulo]);
}
