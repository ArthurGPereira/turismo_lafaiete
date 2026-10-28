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
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['acao'] ?? '') === 'excluir') {

    if ($controller->excluir($_POST['id_evento'] ?? 0)) {
        header('Location: index.php?sucesso=excluido');
        exit;
    }

    $mensagem = 'Não foi possível excluir o evento.';
}

if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'excluido') {
    $mensagem = 'Evento excluído com sucesso!';
}

$eventos = $controller->listar();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos | ArTour</title>
    <link rel="stylesheet" href="../../../css/site.css">
</head>
<body>

<main class="container">
    <h1>Gerenciar eventos</h1>
    <p>Cadastre e gerencie festas, festivais e eventos da região.</p>

    <?php if ($mensagem !== ''): ?>
        <p><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <p>
        <a href="cadastro.php">+ Cadastrar evento</a>
    </p>

    <?php if (empty($eventos)): ?>
        <p>Nenhum evento cadastrado.</p>
    <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Local</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventos as $evento): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($evento['titulo']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($evento['local_evento']) ?>
                        </td>
                        <td>
                            <?= date(
                                'd/m/Y H:i',
                                strtotime($evento['data_inicio'])
                            ) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($evento['status']) ?>
                        </td>
                        <td>
                            <form method="POST"
                                  onsubmit="return confirm('Deseja excluir este evento?')">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden"
                                       name="id_evento"
                                       value="<?= (int) $evento['id_evento'] ?>">

                                <button type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <p><a href="../index.php">Voltar ao painel administrativo</a></p>
</main>

</body>
</html>
