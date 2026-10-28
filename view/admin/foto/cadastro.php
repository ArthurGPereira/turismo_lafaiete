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

$id_local = $_GET['id_local'] ?? $_POST['id_local'] ?? null;

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

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {

        $erro = 'Selecione uma imagem.';

    } else {

        $arquivo = $_FILES['foto'];

        $extensao = strtolower(
            pathinfo($arquivo['name'], PATHINFO_EXTENSION)
        );

        $extensoesPermitidas = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        if (!in_array($extensao, $extensoesPermitidas)) {

            $erro = 'Formato de imagem não permitido. Use JPG, JPEG, PNG ou WEBP.';

        } elseif ($arquivo['size'] > 5 * 1024 * 1024) {

            $erro = 'A imagem deve ter no máximo 5 MB.';

        } else {

            $pasta = '../../../uploads/fotos/';

            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }

            $nomeArquivo = uniqid('foto_', true) . '.' . $extensao;

            $caminhoCompleto = $pasta . $nomeArquivo;

            if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {

                $caminhoBanco = 'uploads/fotos/' . $nomeArquivo;

                $fotoController->cadastrar([
                    'id_local' => $id_local,
                    'caminho' => $caminhoBanco,
                    'descricao' => trim($_POST['descricao'] ?? '')
                ]);

                header('Location: index.php?id_local=' . $id_local);
                exit;

            } else {

                $erro = 'Não foi possível salvar a imagem.';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova Foto - ArTour</title>

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

            <h1>ArTour</h1>

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

                <i class="fa-solid fa-image"></i>

                Nova foto

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
                    Adicionar foto
                </h1>

                <p>
                    Adicione uma imagem para <?= htmlspecialchars($local['nome']) ?>.
                </p>

            </div>

            <div class="admin-section">

                <?php if ($erro): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($erro) ?>

                    </div>

                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <input
                        type="hidden"
                        name="id_local"
                        value="<?= $id_local ?>"
                    >

                    <div class="form-group">

                        <label for="foto">
                            Imagem
                        </label>

                        <input
                            type="file"
                            id="foto"
                            name="foto"
                            accept=".jpg,.jpeg,.png,.webp"
                            required
                        >

                        <small>
                            Formatos permitidos: JPG, JPEG, PNG e WEBP. Máximo: 5 MB.
                        </small>

                    </div>

                    <div class="form-group">

                        <label for="descricao">
                            Descrição
                        </label>

                        <textarea
                            id="descricao"
                            name="descricao"
                            rows="4"
                            placeholder="Ex.: Fachada do local"
                        ></textarea>

                    </div>

                    <div class="admin-form-actions">

                        <a
                            href="index.php?id_local=<?= $id_local ?>"
                            class="btn btn-secondary"
                        >

                            <i class="fa-solid fa-arrow-left"></i>

                            Voltar

                        </a>

                        <button
                            type="submit"
                            class="btn"
                        >

                            <i class="fa-solid fa-upload"></i>

                            Enviar foto

                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>
