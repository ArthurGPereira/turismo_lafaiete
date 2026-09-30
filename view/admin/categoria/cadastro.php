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

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

$categoria = $categoriaController->buscarPorId($id);

if (!$categoria) {
    die('Categoria não encontrada.');
}

if (isset($_POST['editar_categoria'])) {

    $dados = [
        'id_categoria' => $_POST['id_categoria'],
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao']
    ];

    $categoriaController->editar($dados);

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

    <title>Editar Categoria - Tour Local</title>

</head>

<body>

    <header>

        <h1>Tour Local</h1>

        <p>Painel administrativo</p>

    </header>

    <main>

        <h2>Editar Categoria</h2>

        <form method="POST">

            <input
                type="hidden"
                name="id_categoria"
                value="<?= $categoria['id_categoria'] ?>"
            >

            <label for="nome">
                Nome da categoria:
            </label>

            <br>

            <input
                type="text"
                id="nome"
                name="nome"
                value="<?= htmlspecialchars($categoria['nome']) ?>"
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
            ><?= htmlspecialchars($categoria['descricao'] ?? '') ?></textarea>

            <br><br>

            <button
                type="submit"
                name="editar_categoria"
            >
                Salvar alterações
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
