<?php

declare(strict_types=1);

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD MVC de livros</title>
</head>
<body>
    <main>
        <h1>CRUD de livros — responsabilidades separadas</h1>

        <p>
            O Controller recebeu a requisição, o Model trabalhou com os dados
            e esta View ficou responsável pelo HTML.
        </p>

        <?php if ($message !== ''): ?>
            <p><strong><?= escape($message) ?></strong></p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p><strong>Erro:</strong> <?= escape($error) ?></p>
        <?php endif; ?>

        <section>
            <h2><?= $bookBeingEdited === null ? 'Cadastrar livro' : 'Editar livro' ?></h2>

            <form method="post" action="controller.php">
                <input
                    type="hidden"
                    name="action"
                    value="<?= $bookBeingEdited === null ? 'create' : 'update' ?>"
                >

                <?php if ($bookBeingEdited !== null): ?>
                    <input type="hidden" name="id" value="<?= (int) $bookBeingEdited['id'] ?>">
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
                    <?= $bookBeingEdited === null ? 'Cadastrar' : 'Salvar alteração' ?>
                </button>

                <?php if ($bookBeingEdited !== null): ?>
                    <a href="controller.php">Cancelar</a>
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

                                    <form method="post" action="controller.php">
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
