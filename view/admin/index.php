<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../controller/LocalController.php';
require_once '../../controller/UsuarioController.php';
require_once '../../controller/CategoriaController.php';
require_once '../../controller/CidadeController.php';

$localController = new LocalController();
$usuarioController = new UsuarioController();
$categoriaController = new CategoriaController();
$cidadeController = new CidadeController();

$locais = $localController->listar();
$usuarios = $usuarioController->listar();
$categorias = $categoriaController->listar();
$cidades = $cidadeController->listar();

$totalLocais = count($locais);
$totalUsuarios = count($usuarios);
$totalCategorias = count($categorias);
$totalCidades = count($cidades);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel Administrativo - Tour Local</title>

    <link rel="stylesheet" href="../../assets/css/global.css">

    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- Ícones -->
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


            <a href="index.php" class="active">

                <i class="fa-solid fa-chart-line"></i>

                <span>Dashboard</span>

            </a>


            <a href="local/index.php">

                <i class="fa-solid fa-location-dot"></i>

                <span>Locais</span>

            </a>


            <a href="categoria/index.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>Categorias</span>

            </a>


            <a href="cidade/index.php">

                <i class="fa-solid fa-city"></i>

                <span>Cidades</span>

            </a>


            <a href="usuario/index.php">

                <i class="fa-solid fa-users"></i>

                <span>Usuários</span>

            </a>


            <div class="admin-menu-title">
                Sistema
            </div>


            <a href="../perfil.php">

                <i class="fa-solid fa-user"></i>

                <span>Meu perfil</span>

            </a>


        </nav>


        <div class="admin-sidebar-footer">

            <a href="../logout.php">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Sair da conta</span>

            </a>

        </div>

    </aside>



    <!-- CONTEÚDO -->

    <main class="admin-content">


        <!-- CABEÇALHO -->

        <header class="admin-header">


            <h2>Dashboard</h2>


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


            <!-- BOAS-VINDAS -->

            <div class="admin-welcome">

                <h1>

                    Olá, <?= htmlspecialchars($_SESSION['nome_usuario']) ?>!

                </h1>


                <p>

                    Aqui você pode acompanhar e administrar o Tour Local.

                </p>

            </div>



            <!-- ESTATÍSTICAS -->

            <div class="admin-stats">


                <div class="admin-stat">

                    <div class="admin-stat-label">

                        <i class="fa-solid fa-location-dot"></i>

                        Locais cadastrados

                    </div>


                    <div class="admin-stat-number">

                        <?= $totalLocais ?>

                    </div>

                </div>



                <div class="admin-stat">

                    <div class="admin-stat-label">

                        <i class="fa-solid fa-users"></i>

                        Usuários

                    </div>


                    <div class="admin-stat-number">

                        <?= $totalUsuarios ?>

                    </div>

                </div>



                <div class="admin-stat">

                    <div class="admin-stat-label">

                        <i class="fa-solid fa-layer-group"></i>

                        Categorias

                    </div>


                    <div class="admin-stat-number">

                        <?= $totalCategorias ?>

                    </div>

                </div>



                <div class="admin-stat">

                    <div class="admin-stat-label">

                        <i class="fa-solid fa-city"></i>

                        Cidades

                    </div>


                    <div class="admin-stat-number">

                        <?= $totalCidades ?>

                    </div>

                </div>


            </div>



            <!-- LOCAIS -->

            <div class="admin-section">


                <div class="admin-section-header">


                    <div>

                        <h3>

                            <i class="fa-solid fa-location-dot"></i>

                            Locais cadastrados

                        </h3>


                        <p>

                            Últimos locais registrados no sistema.

                        </p>

                    </div>


                    <a href="local/cadastro.php" class="btn">

                        <i class="fa-solid fa-plus"></i>

                        Novo local

                    </a>


                </div>



                <table>


                    <thead>

                        <tr>

                            <th>Nome</th>

                            <th>Categoria</th>

                            <th>Cidade</th>

                        </tr>

                    </thead>



                    <tbody>


                        <?php if (empty($locais)): ?>


                            <tr>

                                <td colspan="3" class="text-center">

                                    Nenhum local cadastrado.

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach (array_slice($locais, 0, 5) as $local): ?>


                                <tr>


                                    <td>

                                        <?= htmlspecialchars($local['nome']) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($local['categoria']) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($local['cidade']) ?>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        <?php endif; ?>


                    </tbody>


                </table>


            </div>



            <!-- ACESSOS RÁPIDOS -->

            <div class="admin-section">


                <div class="admin-section-header">


                    <div>

                        <h3>

                            <i class="fa-solid fa-bolt"></i>

                            Acessos rápidos

                        </h3>


                        <p>

                            Gerencie as principais informações do sistema.

                        </p>

                    </div>


                </div>



                <div class="admin-quick-actions">


                    <a href="local/index.php" class="btn">

                        <i class="fa-solid fa-location-dot"></i>

                        Gerenciar locais

                    </a>


                    <a href="categoria/index.php" class="btn btn-secondary">

                        <i class="fa-solid fa-layer-group"></i>

                        Categorias

                    </a>


                    <a href="cidade/index.php" class="btn btn-secondary">

                        <i class="fa-solid fa-city"></i>

                        Cidades

                    </a>


                    <a href="usuario/index.php" class="btn btn-secondary">

                        <i class="fa-solid fa-users"></i>

                        Usuários

                    </a>


                </div>


            </div>


        </section>


    </main>


</div>


</body>

</html>
