<?php

declare(strict_types=1);

const BOOKS_SESSION_KEY = 'crud_mvc_livros';
const NEXT_ID_SESSION_KEY = 'crud_mvc_proximo_id';

function initializeBooks(): void
{
    if (isset($_SESSION[BOOKS_SESSION_KEY])) {
        return;
    }

    $_SESSION[BOOKS_SESSION_KEY] = [
        1 => ['id' => 1, 'title' => 'Introdução à Programação Web'],
        2 => ['id' => 2, 'title' => 'Laravel na Prática'],
    ];
    $_SESSION[NEXT_ID_SESSION_KEY] = 3;
}

function allBooks(): array
{
    $books = array_values($_SESSION[BOOKS_SESSION_KEY]);
    usort($books, fn (array $first, array $second): int => $first['id'] <=> $second['id']);

    return $books;
}

function findBook(int $id): ?array
{
    return $_SESSION[BOOKS_SESSION_KEY][$id] ?? null;
}

function createBook(string $title): int
{
    $title = normalizeTitle($title);
    $id = (int) $_SESSION[NEXT_ID_SESSION_KEY];

    $_SESSION[BOOKS_SESSION_KEY][$id] = [
        'id' => $id,
        'title' => $title,
    ];
    $_SESSION[NEXT_ID_SESSION_KEY] = $id + 1;

    return $id;
}

function updateBook(int $id, string $title): void
{
    if (findBook($id) === null) {
        throw new RuntimeException('Livro não encontrado.');
    }

    $_SESSION[BOOKS_SESSION_KEY][$id]['title'] = normalizeTitle($title);
}

function deleteBook(int $id): void
{
    if (findBook($id) === null) {
        throw new RuntimeException('Livro não encontrado.');
    }

    unset($_SESSION[BOOKS_SESSION_KEY][$id]);
}

function normalizeTitle(string $title): string
{
    $title = trim($title);

    if ($title === '') {
        throw new InvalidArgumentException('Informe o título do livro.');
    }

    return $title;
}
