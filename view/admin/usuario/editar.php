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

require_once '../../../controller/UsuarioController.php';

$usuarioController = new UsuarioController();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

$usuario = $usuarioController->buscarPorId($id);

if (!$usuario) {
    die('Usuário não encontrado.');
}

if (isset($_POST['editar_usuario'])) {

    $dados = [
        'id_usuario' => $_POST['id_usuario'],
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'tipo' => $_POST['tipo']
    ];

    $usuarioController->editar($dados);

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

    <title>Editar Usuário - ArTour</title>

</head>

<body>

    <header>

        <h1>ArTour</h1>

        <p>Painel administrativo</p>

    </header>

    <main>

        <h2>Editar Usuário</h2>

        <form method="POST">

            <input
                type="hidden"
                name="id_usuario"
                value="<?= $usuario['id_usuario'] ?>"
            >

            <label for="nome">
                Nome:
            </label>

            <br>

            <input
                type="text"
                id="nome"
                name="nome"
                value="<?= htmlspecialchars($usuario['nome']) ?>"
                required
            >

            <br><br>

            <label for="email">
                E-mail:
            </label>

            <br>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($usuario['email']) ?>"
                required
            >

            <br><br>

            <label for="tipo">
                Tipo de usuário:
            </label>

            <br>

            <select id="tipo" name="tipo" required>

                <option
                    value="usuario"
                    <?= $usuario['tipo'] === 'usuario' ? 'selected' : '' ?>
                >
                    Usuário
                </option>

                <option
                    value="administrador"
                    <?= $usuario['tipo'] === 'administrador' ? 'selected' : '' ?>
                >
                    Administrador
                </option>

            </select>

            <br><br>

            <button
                type="submit"
                name="editar_usuario"
            >
                Salvar alterações
            </button>

        </form>

        <br>

        <a href="index.php">
            Voltar para usuários
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
