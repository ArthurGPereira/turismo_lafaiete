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

require_once '../../../dao/LocalDAO.php';
require_once '../../../dao/CategoriaDAO.php';
require_once '../../../controller/LocalController.php';

$localDAO = new LocalDAO();
$categoriaDAO = new CategoriaDAO();

$locais = $localDAO->listar();
$categorias = $categoriaDAO->listar();

$id = isset($_GET['id']) ? $_GET['id'] : null;

$localSelecionado = null;

foreach ($locais as $local) {

    if ($local['id_local'] == $id) {

        $localSelecionado = $local;
        break;
    }
}

if ($localSelecionado === null) {

    die('Local não encontrado.');
}

if (isset($_POST['editar_local'])) {

    $dados = [

        'id_local' => $_POST['id_local'],
        'id_categoria' => $_POST['id_categoria'],
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'],
        'endereco' => $_POST['endereco'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'horario_funcionamento' => $_POST['horario_funcionamento'],
        'preco' => $_POST['preco'] !== ''
            ? $_POST['preco']
            : null
    ];

    $localController = new LocalController();

    $localController->editar($dados);

    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Editar Local - Turismo-Lafa</title>

</head>

<body>

    <h1>Turismo-Lafa</h1>

    <h2>Editar Local</h2>

    <form method="POST">

        <input
            type="hidden"
            name="id_local"
            value="<?= $localSelecionado['id_local'] ?>"
        >

        <label>Nome:</label>
        <br>

        <input
            type="text"
            name="nome"
            value="<?= htmlspecialchars($localSelecionado['nome']) ?>"
            required
        >

        <br><br>

        <label>Categoria:</label>
        <br>

        <select name="id_categoria" required>

            <?php foreach ($categorias as $categoria): ?>

                <option
                    value="<?= $categoria['id_categoria'] ?>"
                    <?= $categoria['id_categoria'] == $localSelecionado['id_categoria']
                        ? 'selected'
                        : '' ?>
                >

                    <?= htmlspecialchars($categoria['nome']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <label>Descrição:</label>
        <br>

        <textarea name="descricao"><?= htmlspecialchars($localSelecionado['descricao']) ?></textarea>

        <br><br>

        <label>Endereço:</label>
        <br>

        <input
            type="text"
            name="endereco"
            value="<?= htmlspecialchars($localSelecionado['endereco']) ?>"
            required
        >

        <br><br>

        <label>Telefone:</label>
        <br>

        <input
            type="text"
            name="telefone"
            value="<?= htmlspecialchars($localSelecionado['telefone'] ?? '') ?>"
        >

        <br><br>

        <label>E-mail:</label>
        <br>

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($localSelecionado['email'] ?? '') ?>"
        >

        <br><br>

        <label>Horário de funcionamento:</label>
        <br>

        <input
            type="text"
            name="horario_funcionamento"
            value="<?= htmlspecialchars($localSelecionado['horario_funcionamento'] ?? '') ?>"
        >

        <br><br>

        <label>Preço:</label>
        <br>

        <input
            type="number"
            name="preco"
            step="0.01"
            value="<?= htmlspecialchars($localSelecionado['preco'] ?? '') ?>"
        >

        <br><br>

        <button type="submit" name="editar_local">
            Salvar alterações
        </button>

    </form>

    <br>

    <a href="index.php">
        Voltar para locais
    </a>

</body>

</html>
