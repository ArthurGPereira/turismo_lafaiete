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

$usuarios = $usuarioController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Usuários - Tour Local</title>

    <link rel="stylesheet" href="../../../assets/css/global.css">
    <link rel="stylesheet" href="../../../assets/css/admin.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="admin-page">

    <!-- SIDEBAR -->

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

                <i class="fa-solid fa-chart-line"></i>

                <span>Dashboard</span>

            </a>


            <a href="../local/index.php">

                <i class="fa-solid fa-location-dot"></i>

                <span>Locais</span>

            </a>


            <a href="../categoria/index.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>Categorias</span>

            </a>


            <a href="../cidade/index.php">

                <i class="fa-solid fa-city"></i>

                <span>Cidades</span>

            </a>


            <a href="index.php" class="active">

                <i class="fa-solid fa-users"></i>

                <span>Usuários</span>

            </a>


            <div class="admin-menu-title">
                Sistema
            </div>


            <a href="../../perfil.php">

                <i class="fa-solid fa-user"></i>

                <span>Meu perfil</span>

            </a>

        </nav>


        <div class="admin-sidebar-footer">

            <a href="../../logout.php">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Sair da conta</span>

            </a>

        </div>

    </aside>


    <!-- CONTEÚDO -->

    <main class="admin-content">


        <header class="admin-header">

            <h2>Usuários</h2>


            <div class="admin-user">

                <div class="admin-user-icon">

                    <?= strtoupper(
                        substr($_SESSION['nome_usuario'], 0, 1)
                    ) ?>

                </div>


                <span>

                    <?= htmlspecialchars($_SESSION['nome_usuario']) ?>

                </span>

            </div>

        </header>


        <section class="admin-main">


            <div class="admin-section">


                <div class="admin-section-header">

                    <div>

                        <h3>

                            <i class="fa-solid fa-users"></i>

                            Usuários cadastrados

                        </h3>

                        <p>
                            Gerencie os usuários do Tour Local.
                        </p>

                    </div>


                    <a
                        href="cadastro.php"
                        class="btn"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Novo usuário

                    </a>

                </div>


                <?php if (isset($_GET['erro']) && $_GET['erro'] === 'proprio_usuario'): ?>

                    <div class="admin-alert admin-alert-error">

                        Você não pode excluir o próprio usuário.

                    </div>

                <?php endif; ?>


                <?php if (empty($usuarios)): ?>

                    <div class="admin-empty">

                        <i class="fa-solid fa-users"></i>

                        <p>
                            Nenhum usuário cadastrado.
                        </p>

                    </div>


                <?php else: ?>


                    <div class="admin-table-container">

                        <table>

                            <thead>

                                <tr>

                                    <th>ID</th>

                                    <th>Nome</th>

                                    <th>E-mail</th>

                                    <th>Tipo</th>

                                    <th>Ações</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($usuarios as $usuario): ?>

                                    <tr>

                                        <td>

                                            <?= (int) $usuario['id_usuario'] ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $usuario['nome']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $usuario['email']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $usuario['tipo']
                                            ) ?>

                                        </td>


                                        <td>

                                            <a
                                                href="editar.php?id=<?= (int) $usuario['id_usuario'] ?>"
                                                class="btn btn-secondary"
                                            >

                                                <i class="fa-solid fa-pen"></i>

                                                Editar

                                            </a>


                                            <?php if (
                                                (int) $usuario['id_usuario']
                                                !==
                                                (int) $_SESSION['id_usuario']
                                            ): ?>

                                                <a
                                                    href="excluir.php?id=<?= (int) $usuario['id_usuario'] ?>"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Tem certeza que deseja excluir este usuário?');"
                                                >

                                                    <i class="fa-solid fa-trash"></i>

                                                    Excluir

                                                </a>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>


            </div>


        </section>


    </main>

</div>

</body>

</html>
