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

require_once '../../../dao/CategoriaDAO.php';
require_once '../../../controller/LocalController.php';

$categoriaDAO = new CategoriaDAO();
$categorias = $categoriaDAO->listar();

if (isset($_POST['cadastrar_local'])) {

    $dados = [
        'id_cidade' => 1,
        'id_categoria' => $_POST['id_categoria'],
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'],
        'endereco' => $_POST['endereco'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'horario_funcionamento' => $_POST['horario_funcionamento'],
        'preco' => $_POST['preco'] !== '' ? $_POST['preco'] : null,
        'latitude' => null,
        'longitude' => null,
        'status' => 'ativo'
    ];

    $localController = new LocalController();

    $localController->cadastrar($dados);

    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Cadastrar Local - Turismo-Lafa</title>

</head>

<body>

    <h1>Turismo-Lafa</h1>

    <h2>Cadastrar Local</h2>

    <form method="POST">

        <label>Nome:</label>
        <br>

        <input
            type="text"
            name="nome"
            required
        >

        <br><br>

        <label>Categoria:</label>
        <br>

        <select name="id_categoria" required>

            <option value="">
                Selecione
            </option>

            <?php foreach ($categorias as $categoria): ?>

                <option value="<?= $categoria['id_categoria'] ?>">

                    <?= htmlspecialchars($categoria['nome']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <label>Descrição:</label>
        <br>

        <textarea name="descricao"></textarea>

        <br><br>

        <label>Endereço:</label>
        <br>

        <input
            type="text"
            name="endereco"
            required
        >

        <br><br>

        <label>Telefone:</label>
        <br>

        <input
            type="text"
            name="telefone"
        >

        <br><br>

        <label>E-mail:</label>
        <br>

        <input
            type="email"
            name="email"
        >

        <br><br>

        <label>Horário de funcionamento:</label>
        <br>

        <input
            type="text"
            name="horario_funcionamento"
        >

        <br><br>

        <label>Preço:</label>
        <br>

        <input
            type="number"
            name="preco"
            step="0.01"
        >

        <br><br>

        <button type="submit" name="cadastrar_local">
            Cadastrar
        </button>

    </form>

    <br>

    <a href="index.php">
        Voltar para locais
    </a>

</body>

</html>
