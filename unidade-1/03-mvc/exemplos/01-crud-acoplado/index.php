<?php

declare(strict_types=1);

session_start();

const BOOKS_SESSION_KEY = 'crud_acoplado_livros';
const NEXT_ID_SESSION_KEY = 'crud_acoplado_proximo_id';
const MESSAGE_SESSION_KEY = 'crud_acoplado_mensagem';

if (!isset($_SESSION[BOOKS_SESSION_KEY])) {
    $_SESSION[BOOKS_SESSION_KEY] = [
        1 => ['id' => 1, 'title' => 'Introdução à Programação Web'],
        2 => ['id' => 2, 'title' => 'Laravel na Prática'],
    ];
    $_SESSION[NEXT_ID_SESSION_KEY] = 3;
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function validateTitle(string $title): string
{
    $title = trim($title);

    if ($title === '') {
        throw new InvalidArgumentException('Informe o título do livro.');
    }

    return $title;
}

$message = (string) ($_SESSION[MESSAGE_SESSION_KEY] ?? '');
unset($_SESSION[MESSAGE_SESSION_KEY]);

$error = '';
$action = (string) ($_POST['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create') {
            $title = validateTitle((string) ($_POST['title'] ?? ''));
            $id = (int) $_SESSION[NEXT_ID_SESSION_KEY];

            $_SESSION[BOOKS_SESSION_KEY][$id] = [
                'id' => $id,
                'title' => $title,
            ];
            $_SESSION[NEXT_ID_SESSION_KEY] = $id + 1;
            $_SESSION[MESSAGE_SESSION_KEY] = 'Livro cadastrado com sucesso.';
        } elseif ($action === 'update') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if ($id === false || !isset($_SESSION[BOOKS_SESSION_KEY][$id])) {
                throw new RuntimeException('Livro não encontrado.');
            }

            $title = validateTitle((string) ($_POST['title'] ?? ''));
            $_SESSION[BOOKS_SESSION_KEY][$id]['title'] = $title;
            $_SESSION[MESSAGE_SESSION_KEY] = 'Livro atualizado com sucesso.';
        } elseif ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if ($id === false || !isset($_SESSION[BOOKS_SESSION_KEY][$id])) {
                throw new RuntimeException('Livro não encontrado.');
            }

            unset($_SESSION[BOOKS_SESSION_KEY][$id]);
            $_SESSION[MESSAGE_SESSION_KEY] = 'Livro excluído com sucesso.';
        } else {
            throw new RuntimeException('Operação inválida.');
        }

        header('Location: index.php');
        exit;
    } catch (InvalidArgumentException | RuntimeException $exception) {
        $error = $exception->getMessage();
    }
}

$editingBook = null;
$editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT);

if ($action === 'update' && $error !== '') {
    $editId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
}

if ($editId !== false && $editId !== null) {
    $editingBook = $_SESSION[BOOKS_SESSION_KEY][$editId] ?? null;
}

$books = array_values($_SESSION[BOOKS_SESSION_KEY]);
usort($books, fn (array $first, array $second): int => $first['id'] <=> $second['id']);

$formTitle = $editingBook['title'] ?? '';

if ($error !== '') {
    $formTitle = (string) ($_POST['title'] ?? $formTitle);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD acoplado de livros</title>
</head>
<body>
    <main>
        <h1>CRUD de livros — tudo em um arquivo</h1>

        <p>
            Este exemplo mistura controle da requisição, manipulação dos dados,
            regras e HTML em <code>index.php</code>.
        </p>

        <?php if ($message !== ''): ?>
            <p><strong><?= escape($message) ?></strong></p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p><strong>Erro:</strong> <?= escape($error) ?></p>
        <?php endif; ?>

        <section>
            <h2><?= $editingBook === null ? 'Cadastrar livro' : 'Editar livro' ?></h2>

            <form method="post" action="index.php">
                <input
                    type="hidden"
                    name="action"
                    value="<?= $editingBook === null ? 'create' : 'update' ?>"
                >

                <?php if ($editingBook !== null): ?>
                    <input type="hidden" name="id" value="<?= (int) $editingBook['id'] ?>">
                <?php endif; ?>

                <label for="title">Título</label>
                <input
                    id="title"
                    name="title"
                    type="text"
                    value="<?= escape((string) $formTitle) ?>"
                    required
                >

                <button type="submit">
                    <?= $editingBook === null ? 'Cadastrar' : 'Salvar alteração' ?>
                </button>

                <?php if ($editingBook !== null): ?>
                    <a href="index.php">Cancelar</a>
                <?php endif; ?>
            </form>
        </section>

        <section>
            <h2>Livros cadastrados</h2>

            <?php if ($books === []): ?>
                <p>Nenhum livro cadastrado.</p>
            <?php else: ?>
                <table border="1" cellpadding="6">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?= (int) $book['id'] ?></td>
                                <td><?= escape((string) $book['title']) ?></td>
                                <td>
                                    <a href="?edit=<?= (int) $book['id'] ?>">Editar</a>

                                    <form method="post" action="index.php">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">
                                        <button type="submit">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
