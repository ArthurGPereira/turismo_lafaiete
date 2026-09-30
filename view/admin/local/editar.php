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

require_once '../../../controller/LocalController.php';
require_once '../../../controller/CategoriaController.php';
require_once '../../../controller/CidadeController.php';

$localController = new LocalController();
$categoriaController = new CategoriaController();
$cidadeController = new CidadeController();

$categorias = $categoriaController->listar();
$cidades = $cidadeController->listar();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

$locais = $localController->listar();

$local = null;

foreach ($locais as $item) {
    if ((int) $item['id_local'] === $id) {
        $local = $item;
        break;
    }
}

if (!$local) {
    echo "Local não encontrado.";
    exit;
}

if (isset($_POST['editar_local'])) {

    $dados = [
        'id_local' => $id,
        'id_cidade' => $_POST['id_cidade'],
        'id_categoria' => $_POST['id_categoria'],
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'],
        'endereco' => $_POST['endereco'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'horario_funcionamento' => $_POST['horario_funcionamento'],
        'preco' => $_POST['preco'] !== '' ? $_POST['preco'] : null
    ];

    $localController->editar($dados);

    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Local - Tour Local</title>

</head>

<body>

<header>

    <h1>Tour Local</h1>

    <p>Painel administrativo</p>

</header>

<main>

    <h2>Editar Local</h2>

    <form method="POST">

        <label for="nome">Nome:</label><br>

        <input
            type="text"
            id="nome"
            name="nome"
            value="<?= htmlspecialchars($local['nome']) ?>"
            required
        >

        <br><br>

        <label for="cidade">Cidade:</label><br>

        <select
            id="cidade"
            name="id_cidade"
            required
        >

            <option value="">Selecione uma cidade</option>

            <?php foreach ($cidades as $cidade): ?>

                <option
                    value="<?= $cidade['id_cidade'] ?>"
                    <?= $local['id_cidade'] == $cidade['id_cidade'] ? 'selected' : '' ?>
                >

                    <?= htmlspecialchars($cidade['nome']) ?> -
                    <?= htmlspecialchars($cidade['estado']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <label for="categoria">Categoria:</label><br>

        <select
            id="categoria"
            name="id_categoria"
            required
        >

            <option value="">Selecione uma categoria</option>

            <?php foreach ($categorias as $categoria): ?>

                <option
                    value="<?= $categoria['id_categoria'] ?>"
                    <?= $local['id_categoria'] == $categoria['id_categoria'] ? 'selected' : '' ?>
                >

                    <?= htmlspecialchars($categoria['nome']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <label for="descricao">Descrição:</label><br>

        <textarea
            id="descricao"
            name="descricao"
            rows="5"
        ><?= htmlspecialchars($local['descricao']) ?></textarea>

        <br><br>

        <label for="endereco">Endereço:</label><br>

        <input
            type="text"
            id="endereco"
            name="endereco"
            value="<?= htmlspecialchars($local['endereco']) ?>"
            required
        >

        <br><br>

        <label for="telefone">Telefone:</label><br>

        <input
            type="text"
            id="telefone"
            name="telefone"
            value="<?= htmlspecialchars($local['telefone']) ?>"
        >

        <br><br>

        <label for="email">E-mail:</label><br>

        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($local['email']) ?>"
        >

        <br><br>

        <label for="horario_funcionamento">
            Horário de funcionamento:
        </label><br>

        <input
            type="text"
            id="horario_funcionamento"
            name="horario_funcionamento"
            value="<?= htmlspecialchars($local['horario_funcionamento']) ?>"
        >

        <br><br>

        <label for="preco">Preço:</label><br>

        <input
            type="number"
            id="preco"
            name="preco"
            step="0.01"
            min="0"
            value="<?= htmlspecialchars($local['preco']) ?>"
        >

        <br><br>

        <button type="submit" name="editar_local">
            Salvar alterações
        </button>

    </form>

    <br>

    <a href="index.php">Voltar para locais</a>

</main>

<footer>

    <p>
        <a href="../../logout.php">Sair</a>
    </p>

</footer>

</body>

</html>
