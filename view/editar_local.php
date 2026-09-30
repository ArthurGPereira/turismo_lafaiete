<?php

require_once '../dao/LocalDAO.php';
require_once '../dao/CategoriaDAO.php';

$localDAO = new LocalDAO();
$categoriaDAO = new CategoriaDAO();

$locais = $localDAO->listar();
$categorias = $categoriaDAO->listar();

$id = isset($_GET['id']) ? $_GET['id'] : null;

$localSelecionado = null;

if ($id !== null) {

    foreach ($locais as $local) {

        if ($local['id_local'] == $id) {

            $localSelecionado = $local;
            break;
        }
    }
}

if ($localSelecionado === null) {

    die("Local não encontrado.");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Editar Local - Tour Local</title>

</head>

<body>

    <h1>Tour Local</h1>

    <h2>Editar Local</h2>

    <form action="../index.php" method="POST">

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
                    <?= $categoria['id_categoria'] == $localSelecionado['id_categoria'] ? 'selected' : '' ?>
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

    <a href="../index.php">
        Voltar
    </a>

</body>

</html>
