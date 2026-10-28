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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

$categoria = $categoriaController->buscarPorId($id);

if (!$categoria) {
    header('Location: index.php');
    exit;
}

$mensagem = '';

if (isset($_POST['editar'])) {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);

    if ($nome === '') {

        $mensagem = 'Informe o nome da categoria.';

    } else {

        $dados = [
            'id_categoria' => $id,
            'nome' => $nome,
            'descricao' => $descricao
        ];

        if ($categoriaController->editar($dados)) {

            header('Location: index.php');
            exit;

        } else {

            $mensagem = 'Não foi possível editar a categoria.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar categoria - ArTour</title>

    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/admin.css?v=2"
    >

    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/categoria.css?v=2"
    >

</head>

<body>

<header>

    <h1>ArTour</h1>

    <p>Painel administrativo</p>

</header>


<main class="categoria-cadastro">

    <div class="categoria-cadastro-topo">

        <div class="categoria-breadcrumb">

            <a href="index.php">
                Categorias
            </a>

            <span>›</span>

            <span>Editar categoria</span>

        </div>


        <h2>
            Editar categoria
        </h2>


        <p>
            Altere as informações da categoria selecionada.
        </p>

    </div>


    <?php if ($mensagem !== ''): ?>

        <div class="categoria-alerta">

            <?= htmlspecialchars($mensagem) ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="categoria-form-card">

            <div class="categoria-form-grupo">

                <label for="nome">
                    Nome da categoria
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($categoria['nome']) ?>"
                    maxlength="100"
                    required
                >

            </div>


            <div class="categoria-form-grupo">

                <label for="descricao">
                    Descrição
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    rows="5"
                ><?= htmlspecialchars($categoria['descricao'] ?? '') ?></textarea>

            </div>


            <div class="categoria-form-acoes">

                <a
                    href="index.php"
                    class="categoria-btn-cancelar"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    name="editar"
                    class="categoria-btn-salvar"
                >
                    Salvar alterações
                </button>

            </div>

        </div>

    </form>

</main>

</body>

</html>
