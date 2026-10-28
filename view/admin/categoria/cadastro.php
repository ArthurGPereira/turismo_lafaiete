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

$mensagem = '';

if (isset($_POST['cadastrar'])) {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);

    if ($nome === '') {

        $mensagem = 'Informe o nome da categoria.';

    } else {

        $dados = [
            'nome' => $nome,
            'descricao' => $descricao
        ];

        if ($categoriaController->cadastrar($dados)) {

            header('Location: index.php');
            exit;

        } else {

            $mensagem = 'Não foi possível cadastrar a categoria.';
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

    <title>Nova categoria - ArTour</title>

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

            <span>Nova categoria</span>

        </div>


        <h2>
            Adicionar categoria
        </h2>


        <p>
            Crie uma categoria para organizar os locais cadastrados no ArTour.
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
                    placeholder="Ex.: Restaurante"
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
                    placeholder="Descreva brevemente essa categoria..."
                    rows="5"
                ></textarea>

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
                    name="cadastrar"
                    class="categoria-btn-salvar"
                >
                    Cadastrar categoria
                </button>

            </div>

        </div>

    </form>

</main>

</body>

</html>
