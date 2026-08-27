<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/model.php';

const MESSAGE_SESSION_KEY = 'crud_mvc_mensagem';

initializeBooks();

$message = (string) ($_SESSION[MESSAGE_SESSION_KEY] ?? '');
unset($_SESSION[MESSAGE_SESSION_KEY]);

$error = '';
$action = (string) ($_POST['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create') {
            createBook((string) ($_POST['title'] ?? ''));
            $_SESSION[MESSAGE_SESSION_KEY] = 'Livro cadastrado com sucesso.';
        } elseif ($action === 'update') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if ($id === false) {
                throw new RuntimeException('Livro não encontrado.');
            }

            updateBook($id, (string) ($_POST['title'] ?? ''));
            $_SESSION[MESSAGE_SESSION_KEY] = 'Livro atualizado com sucesso.';
        } elseif ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if ($id === false) {
                throw new RuntimeException('Livro não encontrado.');
            }

            deleteBook($id);
            $_SESSION[MESSAGE_SESSION_KEY] = 'Livro excluído com sucesso.';
        } else {
            throw new RuntimeException('Operação inválida.');
        }

        header('Location: controller.php');
        exit;
    } catch (InvalidArgumentException | RuntimeException $exception) {
        $error = $exception->getMessage();
    }
}

$editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT);

if ($action === 'update' && $error !== '') {
    $editId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
}

$bookBeingEdited = null;

if ($editId !== false && $editId !== null) {
    $bookBeingEdited = findBook($editId);
}

$formTitle = $bookBeingEdited['title'] ?? '';

if ($error !== '') {
    $formTitle = (string) ($_POST['title'] ?? $formTitle);
}

$books = allBooks();

require __DIR__ . '/view.php';
