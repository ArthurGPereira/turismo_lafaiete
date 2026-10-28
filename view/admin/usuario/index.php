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

    <title>Usuários - ArTour</title>


    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/global.css?v=2"
    >

    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/admin.css?v=2"
    >

    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/usuario.css?v=2"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="admin-page">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="admin-sidebar">

        <div class="admin-brand">

            <h1>ArTour</h1>

            <span>
                Assistente Regional de Turismo
            </span>

        </div>


        <nav class="admin-menu">

            <div class="admin-menu-title">
                Gerenciamento
            </div>


            <a href="../index.php">

                <i class="fa-solid fa-chart-line"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="../local/index.php">

                <i class="fa-solid fa-location-dot"></i>

                <span>
                    Locais
                </span>

            </a>


            <a href="../categoria/index.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>
                    Categorias
                </span>

            </a>


            <a href="../cidade/index.php">

                <i class="fa-solid fa-city"></i>

                <span>
                    Cidades
                </span>

            </a>


            <a href="index.php" class="active">

                <i class="fa-solid fa-users"></i>

                <span>
                    Usuários
                </span>

            </a>


            <div class="admin-menu-title">
                Sistema
            </div>


            <a href="/Turismo-Lafa/index.php">

                <i class="fa-solid fa-house"></i>

                <span>
                    Ver página inicial
                </span>

            </a>


            <a href="../../perfil.php">

                <i class="fa-solid fa-user"></i>

                <span>
                    Meu perfil
                </span>

            </a>

        </nav>


        <div class="admin-sidebar-footer">

            <a href="../../logout.php">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>
                    Sair da conta
                </span>

            </a>

        </div>

    </aside>



    <!-- =====================================================
         CONTEÚDO
    ====================================================== -->

    <main class="admin-content">


        <!-- CABEÇALHO -->

        <header class="admin-header">

            <h2>

                <i class="fa-solid fa-users"></i>

                Usuários

            </h2>


            <div class="admin-user">

                <div class="admin-user-icon">

                    <?= strtoupper(
                        substr($_SESSION['nome_usuario'], 0, 1)
                    ) ?>

                </div>


                <span>

                    <?= htmlspecialchars(
                        $_SESSION['nome_usuario']
                    ) ?>

                </span>

            </div>

        </header>



        <!-- CONTEÚDO PRINCIPAL -->

        <section class="admin-main">


            <div class="admin-section usuario-section">


                <!-- CABEÇALHO DA SEÇÃO -->

                <div class="admin-section-header">

                    <div>

                        <h3>

                            <i class="fa-solid fa-users"></i>

                            Usuários cadastrados

                        </h3>


                        <p>
                            Gerencie os usuários do ArTour.
                        </p>

                    </div>


                    <a
                        href="cadastro.php"
                        class="usuario-btn-novo"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Novo usuário

                    </a>

                </div>



                <!-- ALERTA -->

                <?php if (
                    isset($_GET['erro'])
                    &&
                    $_GET['erro'] === 'proprio_usuario'
                ): ?>

                    <div class="usuario-alert usuario-alert-error">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        <span>
                            Você não pode excluir o próprio usuário.
                        </span>

                    </div>

                <?php endif; ?>



                <!-- LISTAGEM -->

                <?php if (empty($usuarios)): ?>

                    <div class="usuario-vazio">

                        <i class="fa-solid fa-users"></i>

                        <h3>
                            Nenhum usuário cadastrado
                        </h3>

                        <p>
                            Cadastre um usuário para começar.
                        </p>

                    </div>


                <?php else: ?>


                    <div class="usuario-tabela-wrapper">

                        <table class="usuario-tabela">

                            <thead>

                                <tr>

                                    <th class="col-id">
                                        ID
                                    </th>

                                    <th class="col-nome">
                                        Nome
                                    </th>

                                    <th class="col-email">
                                        E-mail
                                    </th>

                                    <th class="col-tipo">
                                        Tipo
                                    </th>

                                    <th class="col-acoes">
                                        Ações
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($usuarios as $usuario): ?>

                                    <tr>


                                        <!-- ID -->

                                        <td class="col-id">

                                            <span class="usuario-id">

                                                #<?= (int) $usuario['id_usuario'] ?>

                                            </span>

                                        </td>



                                        <!-- NOME -->

                                        <td class="col-nome">

                                            <div class="usuario-nome">

                                                <div class="usuario-avatar">

                                                    <?= strtoupper(
                                                        substr(
                                                            $usuario['nome'],
                                                            0,
                                                            1
                                                        )
                                                    ) ?>

                                                </div>

                                                <span>

                                                    <?= htmlspecialchars(
                                                        $usuario['nome']
                                                    ) ?>

                                                </span>

                                            </div>

                                        </td>



                                        <!-- E-MAIL -->

                                        <td class="col-email">

                                            <span class="usuario-email">

                                                <?= htmlspecialchars(
                                                    $usuario['email']
                                                ) ?>

                                            </span>

                                        </td>



                                        <!-- TIPO -->

                                        <td class="col-tipo">

                                            <?php if (
                                                $usuario['tipo']
                                                ===
                                                'administrador'
                                            ): ?>

                                                <span class="usuario-tipo usuario-tipo-admin">

                                                    <i class="fa-solid fa-shield-halved"></i>

                                                    Administrador

                                                </span>

                                            <?php else: ?>

                                                <span class="usuario-tipo usuario-tipo-normal">

                                                    <i class="fa-solid fa-user"></i>

                                                    Usuário

                                                </span>

                                            <?php endif; ?>

                                        </td>



                                        <!-- AÇÕES -->

                                        <td class="col-acoes">

                                            <div class="usuario-acoes">

                                                <a
                                                    href="editar.php?id=<?= (int) $usuario['id_usuario'] ?>"
                                                    class="usuario-editar"
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
                                                        class="usuario-excluir"
                                                        onclick="return confirm('Tem certeza que deseja excluir este usuário?');"
                                                    >

                                                        <i class="fa-solid fa-trash"></i>

                                                        Excluir

                                                    </a>

                                                <?php endif; ?>

                                            </div>

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
