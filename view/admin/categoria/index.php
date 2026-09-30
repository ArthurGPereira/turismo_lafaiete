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

require_once '../../../controller/CategoriaController.php';

$categoriaController = new CategoriaController();

$categorias = $categoriaController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Categorias - Tour Local</title>

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


            <a href="index.php" class="active">

                <i class="fa-solid fa-layer-group"></i>

                <span>Categorias</span>

            </a>


            <a href="../cidade/index.php">

                <i class="fa-solid fa-city"></i>

                <span>Cidades</span>

            </a>


            <a href="../usuario/index.php">

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


        <!-- CABEÇALHO -->

        <header class="admin-header">

            <h2>

                <i class="fa-solid fa-layer-group"></i>

                Categorias

            </h2>


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



        <!-- PRINCIPAL -->

        <section class="admin-main">


            <!-- TÍTULO -->

            <div class="admin-welcome">

                <h1>

                    Gerenciar categorias

                </h1>


                <p>

                    Organize os tipos de locais disponíveis no Tour Local.

                </p>

            </div>



            <!-- TABELA -->

            <div class="admin-section">


                <div class="admin-section-header">


                    <div>

                        <h3>

                            <i class="fa-solid fa-list"></i>

                            Categorias cadastradas

                        </h3>


                        <p>

                            <?= count($categorias) ?>

                            categoria(s) cadastrada(s).

                        </p>

                    </div>


                    <a href="cadastro.php" class="btn">

                        <i class="fa-solid fa-plus"></i>

                        Nova categoria

                    </a>


                </div>



                <table>


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Nome</th>

                            <th>Descrição</th>

                            <th>Ações</th>

                        </tr>

                    </thead>



                    <tbody>


                    <?php if (empty($categorias)): ?>


                        <tr>

                            <td colspan="4" class="text-center">

                                <i class="fa-solid fa-folder-open"></i>

                                Nenhuma categoria cadastrada.

                            </td>

                        </tr>


                    <?php else: ?>


                        <?php foreach ($categorias as $categoria): ?>


                            <tr>


                                <td>

                                    <?= $categoria['id_categoria'] ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $categoria['nome']
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $categoria['descricao']
                                    ) ?>

                                </td>


                                <td>


                                    <a
                                        href="editar.php?id=<?= $categoria['id_categoria'] ?>"
                                        class="btn btn-secondary"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                        Editar

                                    </a>


                                    <a
                                        href="excluir.php?id=<?= $categoria['id_categoria'] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir esta categoria?');"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                        Excluir

                                    </a>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php endif; ?>


                    </tbody>


                </table>


            </div>



            <!-- VOLTAR -->

            <a href="../index.php">

                <i class="fa-solid fa-arrow-left"></i>

                Voltar para o painel

            </a>


        </section>


    </main>


</div>


</body>

</html>
