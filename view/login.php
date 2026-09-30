<?php

session_start();

require_once '../controller/UsuarioController.php';

$mensagem = '';

if (isset($_POST['entrar'])) {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuarioController = new UsuarioController();

    $usuario = $usuarioController->login($email, $senha);

    if ($usuario !== false) {

        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome_usuario'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $usuario['tipo'];

        header('Location: ../index.php');
        exit;

    } else {

        $mensagem = 'E-mail ou senha incorretos.';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Login - Tour Local</title>

</head>

<body>

    <h1>Tour Local</h1>

    <h2>Login</h2>

    <?php if ($mensagem != ''): ?>

        <p>
            <?= htmlspecialchars($mensagem) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>E-mail:</label>
        <br>

        <input
            type="email"
            name="email"
            required
        >

        <br><br>

        <label>Senha:</label>
        <br>

        <input
            type="password"
            name="senha"
            required
        >

        <br><br>

        <button type="submit" name="entrar">
            Entrar
        </button>

    </form>

    <br>

    <a href="../index.php">
        Voltar
    </a>

</body>

</html>
