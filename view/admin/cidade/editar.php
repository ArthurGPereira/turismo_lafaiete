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

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

$cidade = $cidadeController->buscarPorId($id);

if (!$cidade) {
    echo "Cidade não encontrada.";
    exit;
}

if (isset($_POST['editar_cidade'])) {

    $dados = [
        'id_cidade' => $id,
        'nome' => $_POST['nome'],
        'estado' => $_POST['estado'],
        'descricao' => $_POST['descricao'],
        'historia' => $_POST['historia']
    ];

    $cidadeController->editar($dados);

    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Cidade - Tour Local</title>
</head>

<body>

<header>

    <h1>Tour Local</h1>

    <p>Painel administrativo</p>

</header>

<main>

    <h2>Editar Cidade</h2>

    <form method="POST">

        <label for="nome">Nome da cidade:</label><br>

        <input
            type="text"
            id="nome"
            name="nome"
            value="<?= htmlspecialchars($cidade['nome']) ?>"
            required
        >

        <br><br>

        <label for="estado">Estado:</label><br>

        <input
            type="text"
            id="estado"
            name="estado"
            value="<?= htmlspecialchars($cidade['estado']) ?>"
            required
        >

        <br><br>

        <label for="descricao">Descrição:</label><br>

        <textarea
            id="descricao"
            name="descricao"
            rows="5"
        ><?= htmlspecialchars($cidade['descricao']) ?></textarea>

        <br><br>

        <label for="historia">História:</label><br>

        <textarea
            id="historia"
            name="historia"
            rows="8"
        ><?= htmlspecialchars($cidade['historia']) ?></textarea>

        <br><br>

        <button type="submit" name="editar_cidade">
            Salvar alterações
        </button>

    </form>

    <br>

    <a href="index.php">Voltar para cidades</a>

</main>

<footer>

    <p>
        <a href="../../logout.php">Sair</a>
    </p>

</footer>

</body>

</html>
