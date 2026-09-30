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

require_once '../../../controller/FotoController.php';
require_once '../../../controller/LocalController.php';

$fotoController = new FotoController();
$localController = new LocalController();

$id_local = $_GET['id_local'] ?? null;

if (!$id_local) {
    header('Location: ../local/index.php');
    exit;
}

$local = null;

foreach ($localController->listar() as $item) {
    if ($item['id_local'] == $id_local) {
        $local = $item;
        break;
    }
}

if (!$local) {
    header('Location: ../local/index.php');
    exit;
}

$fotos = $fotoController->listarPorLocal($id_local);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fotos - Tour Local</title>

    <link rel="stylesheet" href="../../../assets/css/global.css">

    <link rel="stylesheet" href="../../../assets/css/admin.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

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

                <i class="fa-solid fa-chart-line"></i>

                <span>Dashboard</span>

            </a>

            <a href="../local/index.php" class="active">

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

    <main class="admin-content">

        <header class="admin-header">

            <h2>

                <i class="fa-solid fa-images"></i>

                Fotos

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

        <section class="admin-main">

            <div class="admin-welcome">

                <h1>

                    Fotos de <?= htmlspecialchars($local['nome']) ?>

                </h1>

                <p>

                    Gerencie as imagens deste local.

                </p>

            </div>

            <div class="admin-section">

                <div class="admin-section-header">

                    <div>

                        <h3>

                            <i class="fa-solid fa-images"></i>

                            Fotos cadastradas

                        </h3>

                        <p>

                            <?= count($fotos) ?> foto(s) cadastrada(s).

                        </p>

                    </div>

                    <a
                        href="cadastro.php?id_local=<?= $id_local ?>"
                        class="btn"
                    >

                        <i class="fa-solid fa-plus"></i>

                        Nova foto

                    </a>

                </div>

                <?php if (empty($fotos)): ?>

                    <p class="text-center">

                        <i class="fa-solid fa-image"></i>

                        Nenhuma foto cadastrada para este local.

                    </p>

                <?php else: ?>

                    <div class="fotos-grid">

                        <?php foreach ($fotos as $foto): ?>

                            <div class="foto-card">

                                <img
                                    src="../../../<?= htmlspecialchars($foto['caminho']) ?>"
                                    alt="<?= htmlspecialchars($foto['descricao'] ?? 'Foto do local') ?>"
                                >

                                <div class="foto-card-conteudo">

                                    <p>

                                        <?= htmlspecialchars(
                                            $foto['descricao'] ?? ''
                                        ) ?>

                                    </p>

                                    <a
                                        href="excluir.php?id=<?= $foto['id_foto'] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir esta foto?');"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                        Excluir

                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

            <a href="../local/index.php">

                <i class="fa-solid fa-arrow-left"></i>

                Voltar para locais

            </a>

        </section>

    </main>

</div>

</body>

</html>
