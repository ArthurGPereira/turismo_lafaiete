<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header('Location: ../../index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Painel Administrativo - Turismo-Lafa</title>

</head>

<body>

    <h1>Turismo-Lafa</h1>

    <h2>Painel Administrativo</h2>

    <p>
        Olá,
        <?= htmlspecialchars($_SESSION['nome_usuario']) ?>!
    </p>

    <hr>

    <h3>Gerenciamento</h3>

    <ul>

        <li>
            <a href="../cadastro_local.php">
                Cadastrar local
            </a>
        </li>

        <li>
            <a href="../../index.php">
                Ver locais cadastrados
            </a>
        </li>

    </ul>

    <hr>

    <p>
        <a href="../logout.php">
            Sair
        </a>
    </p>

</body>

</html>
