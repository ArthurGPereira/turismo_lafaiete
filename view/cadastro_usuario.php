<?php

require_once '../controller/UsuarioController.php';

$mensagem = '';

if (isset($_POST['cadastrar'])) {

    $dados = [
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'senha' => $_POST['senha'],
        'tipo' => 'usuario'
    ];

    $usuarioController = new UsuarioController();

    try {

        $usuarioController->cadastrar($dados);

        $mensagem = 'Usuário cadastrado com sucesso!';

    } catch (PDOException $e) {

        $mensagem = 'Erro ao cadastrar. O e-mail pode já estar cadastrado.';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Cadastrar Usuário - Turismo-Lafa</title>

</head>

<body>

    <h1>Turismo-Lafa</h1>

    <h2>Cadastrar usuário</h2>

    <?php if ($mensagem != ''): ?>

        <p>
            <?= htmlspecialchars($mensagem) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Nome:</label>
        <br>

        <input type="text" name="nome" required>

        <br><br>

        <label>E-mail:</label>
        <br>

        <input type="email" name="email" required>

        <br><br>

        <label>Senha:</label>
        <br>

        <input type="password" name="senha" required>

        <br><br>

        <button type="submit" name="cadastrar">
            Cadastrar
        </button>

    </form>

    <br>

    <a href="login.php">
        Ir para o login
    </a>

</body>

</html>
