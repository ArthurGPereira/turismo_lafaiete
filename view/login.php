<?php

session_start();

require_once '../controller/UsuarioController.php';

$mensagem = '';

if (isset($_POST['entrar'])) {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $usuarioController = new UsuarioController();

    $usuario = $usuarioController->login($email, $senha);

    if ($usuario !== false) {

        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome_usuario'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $usuario['tipo'];

        if ($usuario['tipo'] === 'administrador') {
            header('Location: admin/index.php');
        } else {
            header('Location: index.php');
        }

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - Tour Local</title>

    <link rel="stylesheet" href="assets/css/login.css">

</head>

<body>

    <main class="login-page">

        <section class="login-card">

            <div class="login-header">

                <h1>Tour Local</h1>

                <p>
                    Entre na sua conta
                </p>

            </div>


            <?php if ($mensagem != ''): ?>

                <div class="login-error">

                    <?= htmlspecialchars($mensagem) ?>

                </div>

            <?php endif; ?>


            <form method="POST" class="login-form" autocomplete="off">

                <div class="login-group">

                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        autocomplete="off"
                        required
                    >

                </div>


                <div class="login-group">

                    <label for="senha">
                        Senha
                    </label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="entrar"
                    class="login-button"
                >
                    Entrar
                </button>

            </form>


            <div class="login-footer">

                <a href="index.php">
                    Voltar para o início
                </a>

            </div>

        </section>

    </main>

</body>

</html>
