<?php

session_start();

require_once '../controller/UsuarioController.php';

$mensagem = '';

if (!empty($_SESSION['login_erro'])) {
    $mensagem = $_SESSION['login_erro'];
    unset($_SESSION['login_erro']);
}

if (isset($_POST['entrar'])) {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $usuarioController = new UsuarioController();

    $resultado = $usuarioController->login($email, $senha);

    if ($resultado !== false && is_array($resultado) && isset($resultado['tipo']) && strpos($resultado['tipo'], 'empresa_') === 0) {
        $_SESSION['tipo_usuario'] = 'empresa';

        if ($resultado['tipo'] === 'empresa_ativa') {
            $_SESSION['id_usuario'] = $resultado['usuario']['id_usuario'];
            $_SESSION['nome_usuario'] = $resultado['usuario']['nome'];
            $_SESSION['tipo_usuario'] = 'empresa';
            $_SESSION['empresa_status'] = 'ativa';
            header('Location: /Turismo-Lafa/view/empresa/index.php');
            exit;
        }

        if ($resultado['tipo'] === 'empresa_sem_registro') {
            $mensagem = 'Seu acesso não está habilitado.';
            $_SESSION['empresa_status'] = 'inativa';
        } else {
            $mensagem = $resultado['mensagem'];
            $_SESSION['empresa_status'] = match ($resultado['tipo']) {
                'empresa_pendente' => 'pendente',
                'empresa_suspensa' => 'suspensa',
                'empresa_recusada' => 'recusada',
                default => 'inativa',
            };
        }
    } elseif ($resultado !== false && is_array($resultado) && isset($resultado['usuario'])) {
        $_SESSION['id_usuario'] = $resultado['usuario']['id_usuario'];
        $_SESSION['nome_usuario'] = $resultado['usuario']['nome'];
        $_SESSION['tipo_usuario'] = $resultado['usuario']['tipo'];

        if ($resultado['usuario']['tipo'] === 'administrador') {
            header('Location: /Turismo-Lafa/view/admin/index.php');
        } else {
            header('Location: /Turismo-Lafa/index.php');
        }

        exit;
    } elseif ($resultado !== false) {
        $_SESSION['id_usuario'] = $resultado['id_usuario'];
        $_SESSION['nome_usuario'] = $resultado['nome'];
        $_SESSION['tipo_usuario'] = $resultado['tipo'];

        if ($resultado['tipo'] === 'administrador') {
            header('Location: /Turismo-Lafa/view/admin/index.php');
        } else {
            header('Location: /Turismo-Lafa/index.php');
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

    <title>Login - ArTour</title>

    <link
        rel="stylesheet"
        href="../assets/css/login.css"
    >

</head>

<body>

    <main class="login-page">

        <section class="login-card">

            <div class="login-header">

                <h1>
                    ArTour
                </h1>

                <span class="login-slogan">
                    Assistente Regional de Turismo
                </span>

                <p>
                    Entre na sua conta
                </p>

            </div>


            <?php if ($mensagem != ''): ?>

                <div class="login-error">

                    <?= htmlspecialchars($mensagem) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="login-form"
                autocomplete="off"
            >

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

                <p>
                    Ainda não tem uma conta?
                    <a href="cadastro_usuario.php">Cadastre-se</a>
                </p>

                <a href="../index.php">
                    Voltar para o início
                </a>

            </div>

        </section>

    </main>

</body>

</html>
