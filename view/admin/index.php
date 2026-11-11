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

$locaisRecentes = array_slice($locais, 0, 6);
$cidadesRecentes = array_slice($cidades, 0, 5);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - ArTour</title>

    <link
        rel="icon"
        type="image/png"
        href="/Turismo-Lafa/assets/img/artour-simbolo.png"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/global.css?v=3"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/admin.css?v=3"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/dashboard.css?v=1"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="dashboard-page">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="dashboard-sidebar">


        <div class="dashboard-brand">
            <img
                    src="/Turismo-Lafa/assets/img/artour-logo.png"
                    alt="ArTour - Assistente Regional de Turismo"
                >
        </div>


        <nav class="dashboard-menu">


            <span class="dashboard-menu-title">
                Gerenciamento
            </span>


            <a
                href="index.php"
                class="active"
            >

                <i class="fa-solid fa-chart-line"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="local/index.php">

                <i class="fa-solid fa-location-dot"></i>

                <span>
                    Locais
                </span>

            </a>


            <a href="categoria/index.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>
                    Categorias
                </span>

            </a>


            <a href="cidade/index.php">

                <i class="fa-solid fa-city"></i>

                <span>
                    Cidades
                </span>

            </a>


            <a href="empresa/index.php">

                <i class="fa-solid fa-building"></i>

                <span>
                    Empresas
                </span>

            </a>


            <a href="usuario/index.php">

                <i class="fa-solid fa-users"></i>

                <span>
                    Usuários
                </span>

            </a>


            <span class="dashboard-menu-title dashboard-menu-title-system">
                Sistema
            </span>


            <a href="/Turismo-Lafa/index.php">

                <i class="fa-solid fa-house"></i>

                <span>
                    Ver página inicial
                </span>

            </a>


            <a href="../perfil.php">

                <i class="fa-solid fa-user"></i>

                <span>
                    Meu perfil
                </span>

            </a>


        </nav>


        <div class="dashboard-sidebar-footer">

            <a href="../logout.php">

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

    <main class="dashboard-content">


        <!-- CABEÇALHO -->

        <header class="dashboard-header">

            <div class="dashboard-header-title">

                <span>
                    PAINEL ADMINISTRATIVO
                </span>

                <h1>
                    Visão geral
                </h1>

            </div>


            <div class="dashboard-user">

                <div class="dashboard-user-avatar">

                    <?= strtoupper(
                        substr(
                            $_SESSION['nome_usuario'],
                            0,
                            1
                        )
                    ) ?>

                </div>


                <div>

                    <strong>
                        <?= htmlspecialchars(
                            $_SESSION['nome_usuario']
                        ) ?>
                    </strong>

                    <span>
                        Administrador
                    </span>

                </div>

            </div>

        </header>



        <!-- =================================================
             PRINCIPAL
        ================================================== -->

        <section class="dashboard-main">


            <!-- =================================================
                 BOAS-VINDAS
            ================================================== -->

            <section class="dashboard-hero">

                <div class="dashboard-hero-content">

                    <span>
                        ARTOUR
                    </span>

                    <h2>
                        Olá,
                        <?= htmlspecialchars(
                            $_SESSION['nome_usuario']
                        ) ?>.
                    </h2>

                    <p>
                        Gerencie os lugares, cidades e informações
                        que fazem parte da experiência turística
                        do ArTour.
                    </p>

                </div>


                <div class="dashboard-hero-decoration">

                    <i class="fa-solid fa-location-dot"></i>

                </div>

            </section>



            <!-- =================================================
                 ESTATÍSTICAS
            ================================================== -->

            <section class="dashboard-stats">


                <a
                    href="local/index.php"
                    class="dashboard-stat"
                >

                    <div class="dashboard-stat-icon">

                        <i class="fa-solid fa-location-dot"></i>

                    </div>

                    <div class="dashboard-stat-info">

                        <span>
                            Locais cadastrados
                        </span>

                        <strong>
                            <?= $totalLocais ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square dashboard-stat-arrow"></i>

                </a>


                <a
                    href="cidade/index.php"
                    class="dashboard-stat"
                >

                    <div class="dashboard-stat-icon">

                        <i class="fa-solid fa-city"></i>

                    </div>

                    <div class="dashboard-stat-info">

                        <span>
                            Cidades
                        </span>

                        <strong>
                            <?= $totalCidades ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square dashboard-stat-arrow"></i>

                </a>


                <a
                    href="categoria/index.php"
                    class="dashboard-stat"
                >

                    <div class="dashboard-stat-icon">

                        <i class="fa-solid fa-layer-group"></i>

                    </div>

                    <div class="dashboard-stat-info">

                        <span>
                            Categorias
                        </span>

                        <strong>
                            <?= $totalCategorias ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square dashboard-stat-arrow"></i>

                </a>


                <a
                    href="usuario/index.php"
                    class="dashboard-stat"
                >

                    <div class="dashboard-stat-icon">

                        <i class="fa-solid fa-users"></i>

                    </div>

                    <div class="dashboard-stat-info">

                        <span>
                            Usuários
                        </span>

                        <strong>
                            <?= $totalUsuarios ?>
                        </strong>

                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square dashboard-stat-arrow"></i>

                </a>


            </section>



            <!-- =================================================
                 LOCAIS RECENTES
            ================================================== -->

            <section class="dashboard-section">


                <div class="dashboard-section-heading">

                    <div>

                        <span>
                            CONTEÚDO
                        </span>

                        <h2>
                            Lugares cadastrados
                        </h2>

                        <p>
                            Os locais disponíveis atualmente no ArTour.
                        </p>

                    </div>


                    <a
                        href="local/index.php"
                        class="dashboard-section-link"
                    >
                        Ver todos
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>



                <?php if (!empty($locaisRecentes)): ?>


                    <div class="dashboard-place-grid">


                        <?php foreach ($locaisRecentes as $local): ?>


                            <article class="dashboard-place-card">


                                <div class="dashboard-place-image">


                                    <?php if (!empty($local['foto'])): ?>

                                        <img
                                            src="/Turismo-Lafa/<?= htmlspecialchars($local['foto']) ?>"
                                            alt="<?= htmlspecialchars($local['nome']) ?>"
                                        >

                                    <?php else: ?>

                                        <div class="dashboard-place-placeholder">

                                            <i class="fa-solid fa-mountain-sun"></i>

                                        </div>

                                    <?php endif; ?>


                                    <span class="dashboard-place-category">

                                        <?= htmlspecialchars(
                                            $local['categoria']
                                        ) ?>

                                    </span>


                                </div>


                                <div class="dashboard-place-content">


                                    <h3>

                                        <?= htmlspecialchars(
                                            $local['nome']
                                        ) ?>

                                    </h3>


                                    <span class="dashboard-place-city">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <?= htmlspecialchars(
                                            $local['cidade']
                                        ) ?>

                                    </span>


                                    <p>

                                        <?= htmlspecialchars(
                                            $local['descricao'] ??
                                            'Conheça este local.'
                                        ) ?>

                                    </p>


                                    <a
                                        href="local/editar.php?id=<?= (int) $local['id_local'] ?>"
                                    >

                                        Gerenciar local

                                        <i class="fa-solid fa-arrow-right"></i>

                                    </a>


                                </div>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <div class="dashboard-empty">

                        <i class="fa-solid fa-location-dot"></i>

                        <h3>
                            Nenhum local cadastrado
                        </h3>

                        <p>
                            Comece adicionando o primeiro local ao ArTour.
                        </p>

                        <a href="local/cadastro.php">
                            Cadastrar local
                        </a>

                    </div>


                <?php endif; ?>


            </section>



            <!-- =================================================
                 PARTE INFERIOR
            ================================================== -->

            <section class="dashboard-bottom">


                <!-- CIDADES -->

                <div class="dashboard-panel">


                    <div class="dashboard-panel-heading">

                        <div>

                            <span>
                                DESTINOS
                            </span>

                            <h2>
                                Cidades
                            </h2>

                        </div>


                        <a href="cidade/index.php">

                            Ver todas

                        </a>

                    </div>


                    <div class="dashboard-city-list">


                        <?php if (!empty($cidadesRecentes)): ?>


                            <?php foreach ($cidadesRecentes as $cidade): ?>


                                <a
                                    href="cidade/editar.php?id=<?= (int) $cidade['id_cidade'] ?>"
                                    class="dashboard-city-item"
                                >


                                    <div class="dashboard-city-image">


                                        <?php if (!empty($cidade['imagem'])): ?>

                                            <img
                                                src="/Turismo-Lafa/<?= htmlspecialchars($cidade['imagem']) ?>"
                                                alt="<?= htmlspecialchars($cidade['nome']) ?>"
                                            >

                                        <?php else: ?>

                                            <i class="fa-solid fa-city"></i>

                                        <?php endif; ?>


                                    </div>


                                    <div class="dashboard-city-info">

                                        <strong>

                                            <?= htmlspecialchars(
                                                $cidade['nome']
                                            ) ?>

                                        </strong>

                                        <span>

                                            <?= htmlspecialchars(
                                                $cidade['estado']
                                            ) ?>

                                        </span>

                                    </div>


                                    <i class="fa-solid fa-chevron-right"></i>


                                </a>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <p class="dashboard-panel-empty">
                                Nenhuma cidade cadastrada.
                            </p>


                        <?php endif; ?>


                    </div>


                </div>



                <!-- ACESSOS -->

                <div class="dashboard-panel dashboard-actions-panel">


                    <div class="dashboard-panel-heading">

                        <div>

                            <span>
                                ATALHOS
                            </span>

                            <h2>
                                Acessos rápidos
                            </h2>

                        </div>

                    </div>


                    <div class="dashboard-actions">


                        <a
                            href="local/cadastro.php"
                            class="dashboard-action dashboard-action-main"
                        >

                            <div>

                                <i class="fa-solid fa-location-dot"></i>

                            </div>

                            <span>
                                Cadastrar novo local
                            </span>

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>


                        <a
                            href="cidade/cadastro.php"
                            class="dashboard-action"
                        >

                            <div>

                                <i class="fa-solid fa-city"></i>

                            </div>

                            <span>
                                Cadastrar cidade
                            </span>

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>


                        <a
                            href="categoria/cadastro.php"
                            class="dashboard-action"
                        >

                            <div>

                                <i class="fa-solid fa-layer-group"></i>

                            </div>

                            <span>
                                Nova categoria
                            </span>

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>


                        <a
                            href="usuario/cadastro.php"
                            class="dashboard-action"
                        >

                            <div>

                                <i class="fa-solid fa-user-plus"></i>

                            </div>

                            <span>
                                Novo usuário
                            </span>

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>


                    </div>


                </div>


            </section>


        </section>


    </main>


</div>

</body>

</html>
