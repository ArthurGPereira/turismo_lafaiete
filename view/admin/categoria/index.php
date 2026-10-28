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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Categorias - ArTour</title>

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
        href="/Turismo-Lafa/assets/css/categoria.css?v=2"
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


            <a href="index.php" class="active">

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


            <a href="../usuario/index.php">

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


        <!-- =================================================
             CABEÇALHO
        ================================================== -->

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

                    <?= htmlspecialchars(
                        $_SESSION['nome_usuario']
                    ) ?>

                </span>

            </div>

        </header>



        <!-- =================================================
             CONTEÚDO DA PÁGINA
        ================================================== -->

        <section class="admin-main">


            <div class="categoria-lista">


                <!-- =============================================
                     TÍTULO
                ============================================== -->

                <div class="categoria-lista-topo">

                    <div>

                        <div class="categoria-breadcrumb">

                            <a href="../index.php">
                                Painel
                            </a>

                            <span>›</span>

                            <span>
                                Categorias
                            </span>

                        </div>


                        <h2>
                            Categorias
                        </h2>


                        <p>
                            Organize os tipos de lugares disponíveis no ArTour.
                        </p>

                    </div>


                    <a
                        href="cadastro.php"
                        class="categoria-btn-nova"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Adicionar categoria

                    </a>

                </div>



                <!-- =============================================
                     LISTAGEM
                ============================================== -->

                <?php if (empty($categorias)): ?>

                    <div class="categoria-vazia">

                        <h3>
                            Nenhuma categoria cadastrada
                        </h3>

                        <p>
                            Comece adicionando uma categoria para organizar os locais.
                        </p>

                        <a
                            href="cadastro.php"
                            class="categoria-vazia-link"
                        >
                            Adicionar primeira categoria
                        </a>

                    </div>


                <?php else: ?>


                    <div class="categoria-grid">

                        <?php foreach ($categorias as $categoria): ?>

                            <article class="categoria-card">


                                <div class="categoria-card-topo">

                                    <span class="categoria-numero">

                                        #<?= (int) $categoria['id_categoria'] ?>

                                    </span>

                                </div>


                                <div class="categoria-card-conteudo">

                                    <h3>

                                        <?= htmlspecialchars(
                                            $categoria['nome']
                                        ) ?>

                                    </h3>


                                    <p>

                                        <?= htmlspecialchars(
                                            $categoria['descricao']
                                            ?? 'Sem descrição.'
                                        ) ?>

                                    </p>

                                </div>


                                <div class="categoria-card-footer">

                                    <a
                                        href="editar.php?id=<?= (int) $categoria['id_categoria'] ?>"
                                        class="categoria-editar"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                        Editar

                                    </a>


                                    <a
                                        href="excluir.php?id=<?= (int) $categoria['id_categoria'] ?>"
                                        class="categoria-excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir esta categoria?');"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                        Excluir

                                    </a>

                                </div>


                            </article>

                        <?php endforeach; ?>

                    </div>


                <?php endif; ?>


                <a
                    href="../index.php"
                    class="categoria-voltar"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Voltar para o painel

                </a>


            </div>


        </section>


    </main>


</div>

</body>

</html>
