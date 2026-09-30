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

if (isset($_POST['cadastrar_local'])) {

    $dados = [
        'id_cidade' => $_POST['id_cidade'],
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

    $localController->cadastrar($dados);

    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Local - Tour Local</title>
</head>

<body>

<header>

    <h1>Tour Local</h1>

    <p>Painel administrativo</p>

</header>

<main>

    <h2>Cadastrar Local</h2>

    <form method="POST">

        <label for="nome">Nome:</label><br>

        <input
            type="text"
            id="nome"
            name="nome"
            required
        >

        <br><br>

        <label for="cidade">Cidade:</label><br>

        <select id="cidade" name="id_cidade" required>

            <option value="">Selecione uma cidade</option>

            <?php foreach ($cidades as $cidade): ?>

                <option value="<?= $cidade['id_cidade'] ?>">
                    <?= htmlspecialchars($cidade['nome']) ?> -
                    <?= htmlspecialchars($cidade['estado']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <label for="categoria">Categoria:</label><br>

        <select id="categoria" name="id_categoria" required>

            <option value="">Selecione uma categoria</option>

            <?php foreach ($categorias as $categoria): ?>

                <option value="<?= $categoria['id_categoria'] ?>">
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
        ></textarea>

        <br><br>

        <label for="endereco">Endereço:</label><br>

        <input
            type="text"
            id="endereco"
            name="endereco"
            required
        >

        <br><br>

        <label for="telefone">Telefone:</label><br>

        <input
            type="text"
            id="telefone"
            name="telefone"
        >

        <br><br>

        <label for="email">E-mail:</label><br>

        <input
            type="email"
            id="email"
            name="email"
        >

        <br><br>

        <label for="horario_funcionamento">Horário de funcionamento:</label><br>

        <input
            type="text"
            id="horario_funcionamento"
            name="horario_funcionamento"
        >

        <br><br>

        <label for="preco">Preço:</label><br>

        <input
            type="number"
            id="preco"
            name="preco"
            step="0.01"
            min="0"
        >

        <br><br>

        <button type="submit" name="cadastrar_local">
            Cadastrar local
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
