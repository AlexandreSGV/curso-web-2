<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de livro com MVC</title>
</head>
<body>
    <h1>Cadastro de livro — responsabilidades separadas</h1>

    <p>
        O Controller recebeu os dados, o Model realizou o cadastro
        e a View apresenta este formulário.
    </p>

    <?php if ($mensagem !== ''): ?>
        <p><?= $mensagem ?></p>
    <?php endif; ?>

    <?php if ($erro !== ''): ?>
        <p><?= $erro ?></p>
    <?php endif; ?>

    <form method="post" action="controller.php">
        <label for="titulo">Título</label>
        <input
            id="titulo"
            name="titulo"
            value="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>"
            required
        >
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
