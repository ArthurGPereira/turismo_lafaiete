<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header('Location: ../../../index.php');
    exit;
}

require_once '../../../controller/UsuarioController.php';

$usuarioController = new UsuarioController();

$mensagem = '';

if (isset($_POST['cadastrar'])) {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    if ($nome === '' || $email === '' || $senha === '') {

        $mensagem = 'Preencha todos os campos obrigatórios.';

    } else {

        $dados = [
            'nome' => $nome,
            'email' => $email,
            'senha' => $senha,
            'tipo' => $tipo,
            'imagem' => null
        ];

        if ($usuarioController->cadastrar($dados)) {

            header('Location: index.php');
            exit;

        } else {

            $mensagem = 'Não foi possível cadastrar o usuário.';
        }
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

    <title>Novo usuário - Tour Local</title>

    <link rel="stylesheet" href="../../../assets/css/global.css">
    <link rel="stylesheet" href="../../../assets/css/admin.css">

</head>

<body>

<div class="admin-page">

    <aside class="admin-sidebar">

        <div class="admin-brand">

            <h1>Tour Local</h1>

            <span>Painel administrativo</span>

        </div>

        <nav class="admin-menu">

            <div class="admin-menu-title">
                Gerenciamento
            </div>

            <a href="../index.php">
                <span>Dashboard</span>
            </a>

            <a href="../local/index.php">
                <span>Locais</span>
            </a>

            <a href="../categoria/index.php">
                <span>Categorias</span>
            </a>

            <a href="../cidade/index.php">
                <span>Cidades</span>
            </a>

            <a href="index.php" class="active">
                <span>Usuários</span>
            </a>

        </nav>

        <div class="admin-sidebar-footer">

            <a href="../../logout.php">
                <span>Sair da conta</span>
            </a>

        </div>

    </aside>


    <main class="admin-content">

        <header class="admin-header">

            <h2>Novo usuário</h2>

        </header>


        <section class="admin-main">

            <div class="admin-section">

                <div class="admin-section-header">

                    <div>

                        <h3>Cadastrar usuário</h3>

                        <p>
                            Adicione um novo usuário ao sistema.
                        </p>

                    </div>

                </div>


                <?php if ($mensagem !== ''): ?>

                    <div class="admin-alert admin-alert-error">

                        <?= htmlspecialchars($mensagem) ?>

                    </div>

                <?php endif; ?>


                <form method="POST">

                    <div class="admin-form-group">

                        <label for="nome">
                            Nome
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            required
                        >

                    </div>


                    <div class="admin-form-group">

                        <label for="email">
                            E-mail
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                        >

                    </div>


                    <div class="admin-form-group">

                        <label for="senha">
                            Senha
                        </label>

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            required
                        >

                    </div>


                    <div class="admin-form-group">

                        <label for="tipo">
                            Tipo de usuário
                        </label>

                        <select
                            id="tipo"
                            name="tipo"
                            required
                        >

                            <option value="usuario">
                                Usuário
                            </option>

                            <option value="administrador">
                                Administrador
                            </option>

                        </select>

                    </div>


                    <div class="admin-form-actions">

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            name="cadastrar"
                            class="btn"
                        >
                            Cadastrar usuário
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>
