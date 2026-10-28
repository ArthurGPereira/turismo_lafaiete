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

require_once '../../../controller/CidadeController.php';

$cidadeController = new CidadeController();

$cidades = $cidadeController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cidades - ArTour</title>

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
        href="/Turismo-Lafa/assets/css/cidade.css?v=2"
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


            <a href="index.php" class="active">

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

                <i class="fa-solid fa-city"></i>

                Cidades

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


            <div class="cidade-lista">


                <!-- =============================================
                     TÍTULO
                ============================================== -->

                <div class="cidade-lista-topo">

                    <div>

                        <div class="cidade-breadcrumb">

                            <a href="../index.php">
                                Painel
                            </a>

                            <span>›</span>

                            <span>
                                Cidades
                            </span>

                        </div>


                        <h2>
                            Cidades
                        </h2>


                        <p>
                            Explore e gerencie os destinos disponíveis no ArTour.
                        </p>

                    </div>


                    <a
                        href="cadastro.php"
                        class="cidade-btn-nova"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Adicionar cidade

                    </a>

                </div>



                <!-- =============================================
                     LISTAGEM
                ============================================== -->

                <?php if (empty($cidades)): ?>

                    <div class="cidade-vazia">

                        <h3>
                            Nenhuma cidade cadastrada
                        </h3>

                        <p>
                            Comece adicionando o primeiro destino ao ArTour.
                        </p>

                        <a
                            href="cadastro.php"
                            class="cidade-vazia-link"
                        >
                            Adicionar primeira cidade
                        </a>

                    </div>


                <?php else: ?>


                    <div class="cidade-grid">

                        <?php foreach ($cidades as $cidade): ?>

                            <article class="cidade-card">


                                <!-- IMAGEM -->

                                <div class="cidade-imagem">

                                    <?php if (!empty($cidade['imagem'])): ?>

                                        <img
                                            src="../../../<?= htmlspecialchars($cidade['imagem']) ?>"
                                            alt="<?= htmlspecialchars($cidade['nome']) ?>"
                                        >

                                    <?php else: ?>

                                        <div class="cidade-sem-imagem">

                                            <i class="fa-solid fa-city"></i>

                                            <span>
                                                Sem imagem
                                            </span>

                                        </div>

                                    <?php endif; ?>

                                </div>



                                <!-- CONTEÚDO -->

                                <div class="cidade-card-conteudo">

                                    <span class="cidade-estado">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <?= htmlspecialchars(
                                            $cidade['estado']
                                        ) ?>

                                    </span>


                                    <h3>

                                        <?= htmlspecialchars(
                                            $cidade['nome']
                                        ) ?>

                                    </h3>


                                    <p>

                                        <?= htmlspecialchars(
                                            $cidade['descricao']
                                            ?? 'Sem descrição.'
                                        ) ?>

                                    </p>



                                    <!-- AÇÕES -->

                                    <div class="cidade-card-footer">

                                        <a
                                            href="editar.php?id=<?= (int) $cidade['id_cidade'] ?>"
                                            class="cidade-editar"
                                        >

                                            <i class="fa-solid fa-pen"></i>

                                            Editar

                                        </a>


                                        <a
                                            href="excluir.php?id=<?= (int) $cidade['id_cidade'] ?>"
                                            class="cidade-excluir"
                                            onclick="return confirm('Tem certeza que deseja excluir esta cidade?');"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                            Excluir

                                        </a>

                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>


                <?php endif; ?>


                <!-- VOLTAR -->

                <a
                    href="../index.php"
                    class="cidade-voltar"
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
