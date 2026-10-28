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

require_once '../../../controller/LocalController.php';

$localController = new LocalController();

$locais = $localController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciar Locais - ArTour</title>

    <link rel="stylesheet" href="../../../assets/css/global.css">
    <link rel="stylesheet" href="../../../assets/css/admin.css">
    <link rel="stylesheet" href="../../../assets/css/local.css?v=99">

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


            <a href="index.php" class="active">

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



    <!-- CONTEÚDO -->

    <main class="admin-content">


        <!-- CABEÇALHO -->

        <header class="admin-header">

            <h2>

                <i class="fa-solid fa-location-dot"></i>

                Locais

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



        <!-- PRINCIPAL -->

        <section class="admin-main">


            <!-- TÍTULO -->

            <div class="admin-welcome">

                <h1>
                    Gerenciar locais
                </h1>

                <p>
                    Cadastre e gerencie os pontos turísticos,
                    restaurantes e espaços disponíveis no ArTour.
                </p>

            </div>



            <!-- LISTAGEM -->

            <div class="admin-section">


                <div class="admin-section-header">

                    <div>

                        <h3>

                            <i class="fa-solid fa-list"></i>

                            Locais cadastrados

                        </h3>

                        <p>

                            <?= count($locais) ?>

                            local(is) cadastrado(s).

                        </p>

                    </div>


                    <a
                        href="cadastro.php"
                        class="btn"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Novo local

                    </a>

                </div>



                <?php if (!empty($locais)): ?>


                    <table class="locais-tabela">

                        <thead>

                            <tr>

                                <th>
                                    Nome
                                </th>

                                <th>
                                    Categoria
                                </th>

                                <th>
                                    Cidade
                                </th>

                                <th>
                                    Endereço
                                </th>

                                <th>
                                    Ações
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach ($locais as $local): ?>


                            <tr>


                                <td>

                                    <?= htmlspecialchars(
                                        $local['nome']
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $local['categoria']
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $local['cidade']
                                    ) ?>

                                </td>


                                <td class="local-endereco">

                                    <?= htmlspecialchars(
                                        $local['endereco']
                                    ) ?>

                                </td>


                                <td>


                                    <a
                                        href="editar.php?id=<?= (int) $local['id_local'] ?>"
                                        class="btn btn-secondary"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                        Editar

                                    </a>


                                    <a
                                        href="../foto/index.php?id_local=<?= (int) $local['id_local'] ?>"
                                        class="btn btn-secondary"
                                    >

                                        <i class="fa-solid fa-images"></i>

                                        Fotos

                                    </a>


                                    <a
                                        href="excluir.php?id=<?= (int) $local['id_local'] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir este local?');"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                        Excluir

                                    </a>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>


                <?php else: ?>


                    <p class="text-center">

                        <i class="fa-solid fa-location-dot"></i>

                        Nenhum local cadastrado.

                    </p>


                <?php endif; ?>


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
