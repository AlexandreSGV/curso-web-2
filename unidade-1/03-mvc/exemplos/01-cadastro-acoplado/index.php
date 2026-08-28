<?php

$mensagem = '';
$erro = '';
$titulo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');

    if ($titulo === '') {
        $erro = 'Informe o título do livro.';
    } else {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;dbname=biblioteca;charset=utf8mb4',
            'biblioteca',
            getenv('DB_PASSWORD') ?: ''
        );

        $comando = $pdo->prepare(
            'INSERT INTO livros (titulo) VALUES (:titulo)'
        );
        $comando->execute(['titulo' => $titulo]);

        $mensagem = 'Livro cadastrado com sucesso.';
        $titulo = '';
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de livro</title>
</head>
<body>
    <h1>Cadastro de livro — tudo em um arquivo</h1>

    <p>
        Este exemplo mistura o formulário, o controle da requisição
        e o acesso ao banco de dados em <code>index.php</code>.
    </p>

    <?php if ($mensagem !== ''): ?>
        <p><?= $mensagem ?></p>
    <?php endif; ?>

    <?php if ($erro !== ''): ?>
        <p><?= $erro ?></p>
    <?php endif; ?>

    <form method="post" action="index.php">
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
