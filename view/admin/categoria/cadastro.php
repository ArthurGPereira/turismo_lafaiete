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

if (isset($_POST['cadastrar_categoria'])) {

    $dados = [
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao']
    ];

    $categoriaController->cadastrar($dados);

    header('Location: index.php');
    exit;
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

    <title>Cadastrar Categoria - Turismo-Lafa</title>

</head>

<body>

    <header>

        <h1>Turismo-Lafa</h1>

        <p>Painel administrativo</p>

    </header>

    <main>

        <h2>Cadastrar Categoria</h2>

        <form method="POST">

            <label for="nome">
                Nome da categoria:
            </label>

            <br>

            <input
                type="text"
                id="nome"
                name="nome"
                required
            >

            <br><br>

            <label for="descricao">
                Descrição:
            </label>

            <br>

            <textarea
                id="descricao"
                name="descricao"
                rows="5"
            ></textarea>

            <br><br>

            <button
                type="submit"
                name="cadastrar_categoria"
            >
                Cadastrar
            </button>

        </form>

        <br>

        <a href="index.php">
            Voltar para categorias
        </a>

    </main>

    <footer>

        <p>
            <a href="../../logout.php">
                Sair
            </a>
        </p>

    </footer>

</body>

</html>
