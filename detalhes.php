
<?php
require_once __DIR__ . '/controller/EventoController.php';

$idEvento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idEvento || $idEvento < 1) {
    http_response_code(400);
    exit('Evento inválido. Volte à lista de eventos e selecione um evento.');
}

try {
    $controller = new EventoController();
    $evento = $controller->buscarPorId($idEvento);

    if (!$evento || $evento['status'] !== 'ativo') {
        http_response_code(404);
        exit('Evento não encontrado ou indisponível.');
    }
} catch (Throwable $e) {
    http_response_code(500);
    exit('Não foi possível carregar os detalhes do evento.');
}

function escapar($valor)
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escapar($evento['titulo']) ?> | ArTour</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7f7;
            color: #18383d;
        }
        header {
            background: #073F4D;
            color: white;
            padding: 22px 7%;
        }
        header a { color: white; text-decoration: none; font-weight: bold; }
        main {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .evento {
            background: white;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 8px 28px #17383d12;
        }
        h1 { margin-top: 0; color: #073F4D; }
        .informacao { margin: 18px 0; line-height: 1.7; }
        .data {
            display: inline-block;
            background: #e4f2ef;
            color: #073F4D;
            padding: 9px 14px;
            border-radius: 8px;
        }
        .voltar {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 18px;
            background: #073F4D;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php">ArTour — Assistente Regional de Turismo</a>
    </header>

    <main>
        <article class="evento">
            <h1><?= escapar($evento['titulo']) ?></h1>

            <p class="data">
                Início: <?= escapar(date('d/m/Y H:i', strtotime($evento['data_inicio']))) ?>
            </p>

            <?php if (!empty($evento['data_fim'])): ?>
                <p class="data">
                    Término: <?= escapar(date('d/m/Y H:i', strtotime($evento['data_fim']))) ?>
                </p>
            <?php endif; ?>

            <div class="informacao">
                <h2>Sobre o evento</h2>
                <p><?= nl2br(escapar($evento['descricao'])) ?></p>
            </div>

            <div class="informacao">
                <h2>Localização</h2>
                <p><strong>Local:</strong> <?= escapar($evento['local_evento']) ?></p>
                <p><strong>Endereço:</strong> <?= escapar($evento['endereco']) ?></p>
            </div>

            <a class="voltar" href="index.php">Voltar ao início</a>
        </article>
    </main>
</body>
</html>
