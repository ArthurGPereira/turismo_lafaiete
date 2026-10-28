<?php
session_start();

if (
    !isset($_SESSION['id_usuario']) ||
    ($_SESSION['tipo_usuario'] ?? '') !== 'administrador'
) {
    header('Location: ../../../index.php');
    exit;
}

require_once __DIR__ . '/../../../controller/EventoController.php';

$controller = new EventoController();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($controller->cadastrar()) {
        header('Location: index.php');
        exit;
    }

    $erro = 'Confira os campos e as datas informadas.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar evento | ArTour</title>
    <link rel="stylesheet" href="../../../css/site.css">
</head>
<body>

<main class="container">
    <h1>Cadastrar evento</h1>

    <?php if ($erro !== ''): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="titulo">Nome do evento</label>
        <input type="text" id="titulo" name="titulo"
               maxlength="150" required>

        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" required></textarea>

        <label for="local_evento">Local do evento</label>
        <input type="text" id="local_evento" name="local_evento"
               maxlength="150" required>

        <label for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco"
               maxlength="255" required>

        <label for="data_inicio">Data e horário de início</label>
        <input type="datetime-local" id="data_inicio"
               name="data_inicio" required>

        <label for="data_fim">Data e horário de encerramento</label>
        <input type="datetime-local" id="data_fim" name="data_fim">

        <button type="submit">Salvar evento</button>
        <a href="index.php">Cancelar</a>
    </form>
</main>

</body>
</html>
