<?php

session_start();

require_once '../controller/LocalController.php';
require_once '../controller/FotoController.php';
require_once '../controller/FavoritoController.php';
require_once '../controller/AvaliacaoController.php';

$localController = new LocalController();
$fotoController = new FotoController();
$favoritoController = new FavoritoController();
$avaliacaoController = new AvaliacaoController();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_favorito'])) {
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }

    $acao = $_POST['acao_favorito'];
    $idUsuario = (int) $_SESSION['id_usuario'];

    if ($acao === 'adicionar') {
        $favoritoController->adicionar($idUsuario, $id);
    }

    if ($acao === 'remover') {
        $favoritoController->remover($idUsuario, $id);
    }

    header('Location: local.php?id=' . (int) $id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_avaliacao'])) {
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }

    $resultadoAvaliacao = $avaliacaoController->avaliarLocal([
        'id_usuario' => $_SESSION['id_usuario'],
        'id_local' => $id,
        'nota' => $_POST['nota'] ?? null,
        'comentario' => $_POST['comentario'] ?? ''
    ]);

    $_SESSION['avaliacao_mensagem'] = $resultadoAvaliacao['mensagem'];
    $_SESSION['avaliacao_sucesso'] = $resultadoAvaliacao['sucesso'];

    header('Location: local.php?id=' . (int) $id);
    exit;
}

$local = $localController->buscarPorId($id);

if (!$local) {
    header('Location: ../index.php');
    exit;
}

$fotos = $fotoController->listarPorLocal($id);
$usuarioLogado = $_SESSION['id_usuario'] ?? null;
$favoritado = false;

if ($usuarioLogado) {
    $favoritado = $favoritoController->verificarFavorito($usuarioLogado, $id);
}

$mensagemAvaliacao = $_SESSION['avaliacao_mensagem'] ?? null;
$avaliacaoSucesso = $_SESSION['avaliacao_sucesso'] ?? null;
unset($_SESSION['avaliacao_mensagem'], $_SESSION['avaliacao_sucesso']);

$avaliacoes = $avaliacaoController->listarPorLocal($id);
$mediaAvaliacao = $avaliacaoController->mediaPorLocal($id);

function caminhoImagem($caminho)
{
    if (empty($caminho)) {
        return null;
    }

    if (strpos($caminho, '/Turismo-Lafa/') === 0) {
        return $caminho;
    }

    return '/Turismo-Lafa/' . ltrim($caminho, '/');
}

$fotoPrincipal = !empty($fotos)
    ? caminhoImagem($fotos[0]['caminho'])
    : null;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($local['nome']) ?> - ArTour
    </title>

    <link
        rel="icon"
        type="image/png"
        href="/Turismo-Lafa/assets/img/artour-simbolo.png"
    >

    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/global.css?v=4"
    >

    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/site.css?v=4"
    >

    <link
        rel="stylesheet"
        href="/Turismo-Lafa/assets/css/local-detalhes.css?v=1"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

    <style>
        .local-favorito-wrap {
            margin-top: 1.25rem;
        }

        .local-favorito-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 0;
            border-radius: 999px;
            padding: 0.8rem 1.2rem;
            background: #f39c12;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }

        .local-favorito-button:hover {
            opacity: 0.92;
        }

        .local-favorito-login {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.25rem;
            color: #1a3c5d;
            font-weight: 600;
            text-decoration: none;
        }

        .avaliacao-resumo {
            margin-top: 2rem;
            padding: 1rem 1.2rem;
            background: #f8fafc;
            border: 1px solid #dde7f1;
            border-radius: 12px;
        }

        .avaliacao-resumo h2,
        .avaliacao-bloco h2 {
            margin: 0 0 0.75rem;
            font-size: 1.2rem;
        }

        .avaliacao-media {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }

        .avaliacao-nota {
            font-size: 1.5rem;
            font-weight: 700;
            color: #d97706;
        }

        .avaliacao-form {
            margin-top: 1.5rem;
            padding: 1rem 1.2rem;
            border: 1px solid #dde7f1;
            background: #fff;
            border-radius: 12px;
        }

        .avaliacao-form fieldset {
            border: 0;
            padding: 0;
            margin: 0;
        }

        .avaliacao-estrelas {
            display: flex;
            gap: 0.45rem;
            margin: 0.8rem 0 1rem;
            flex-wrap: wrap;
        }

        .avaliacao-estrela {
            width: 2.5rem;
            height: 2.5rem;
            padding: 0;
            border: 0;
            background: transparent;
            color: #cbd5e1;
            font-size: 2rem;
            line-height: 1;
            cursor: pointer;
            transition: transform 0.15s ease, color 0.15s ease, opacity 0.15s ease;
        }

        .avaliacao-estrela:hover,
        .avaliacao-estrela:focus-visible {
            color: #f6b942;
            outline: none;
            transform: translateY(-1px);
        }

        .avaliacao-estrela.ativa {
            color: #f59e0b;
        }

        .avaliacao-estrela:focus-visible {
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.18);
            border-radius: 999px;
        }

        .avaliacao-textarea {
            width: 100%;
            min-height: 110px;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            resize: vertical;
        }

        .avaliacao-botao {
            margin-top: 1rem;
            border: 0;
            border-radius: 10px;
            background: #1a3c5d;
            color: #fff;
            padding: 0.8rem 1.2rem;
            font-weight: 700;
            cursor: pointer;
        }

        .avaliacao-mensagem {
            margin-top: 1rem;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-weight: 600;
        }

        .avaliacao-mensagem.sucesso {
            background: #e8f7ee;
            color: #166534;
        }

        .avaliacao-mensagem.erro {
            background: #fdecec;
            color: #991b1b;
        }

        .avaliacao-lista {
            margin-top: 1rem;
            display: grid;
            gap: 1rem;
        }

        .avaliacao-item {
            border: 1px solid #dde7f1;
            border-radius: 12px;
            background: #fff;
            padding: 1rem;
        }

        .avaliacao-topo {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 0.5rem;
        }

        .avaliacao-autor {
            font-weight: 700;
        }

        .avaliacao-data {
            color: #64748b;
            font-size: 0.9rem;
        }

        .avaliacao-comentario {
            color: #334155;
            line-height: 1.6;
            white-space: pre-line;
        }

        @media (max-width: 768px) {
            .avaliacao-topo {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<header class="header">

    <div class="container">

        <div class="logo">
            <a href="/Turismo-Lafa/index.php">
                ArTour
            </a>
        </div>

        <nav class="menu">

            <a href="/Turismo-Lafa/index.php">
                Início
            </a>

            <a href="/Turismo-Lafa/index.php#categorias">
                Categorias
            </a>

            <a href="/Turismo-Lafa/index.php#locais">
                Locais
            </a>

            <a href="/Turismo-Lafa/index.php#eventos">
                Eventos
            </a>

            <a
                href="/Turismo-Lafa/view/login.php"
                class="menu-login"
            >
                Entrar
            </a>

        </nav>

    </div>

</header>


<main class="local-detalhes-page">

    <div class="container">

        <a
            href="/Turismo-Lafa/index.php"
            class="local-voltar"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Voltar para os locais
        </a>


        <section class="local-detalhes">

            <div class="local-galeria">

                <div class="local-foto-principal">

                    <?php if ($fotoPrincipal): ?>

                        <img
                            src="<?= htmlspecialchars($fotoPrincipal) ?>"
                            alt="<?= htmlspecialchars($local['nome']) ?>"
                        >

                    <?php else: ?>

                        <div class="local-sem-foto">
                            <i class="fa-solid fa-mountain-sun"></i>

                            <span>
                                Foto não disponível
                            </span>
                        </div>

                    <?php endif; ?>

                </div>


                <?php if (count($fotos) > 1): ?>

                    <div class="local-miniaturas">

                        <?php foreach ($fotos as $foto): ?>

                            <?php
                            $caminho = caminhoImagem($foto['caminho']);
                            ?>

                            <div class="local-miniatura">

                                <img
                                    src="<?= htmlspecialchars($caminho) ?>"
                                    alt="<?= htmlspecialchars($foto['descricao'] ?? $local['nome']) ?>"
                                >

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>


            <div class="local-informacoes">

                <span class="local-categoria">
                    <?= htmlspecialchars($local['categoria']) ?>
                </span>

                <h1>
                    <?= htmlspecialchars($local['nome']) ?>
                </h1>

                <div class="local-cidade">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>
                        <?= htmlspecialchars($local['cidade']) ?>
                        -
                        <?= htmlspecialchars($local['estado']) ?>
                    </span>
                </div>

                <div class="local-favorito-wrap">
                    <?php if (isset($_SESSION['id_usuario'])): ?>
                        <form method="POST" action="local.php?id=<?= (int) $id ?>">
                            <input
                                type="hidden"
                                name="acao_favorito"
                                value="<?= $favoritado ? 'remover' : 'adicionar' ?>"
                            >
                            <button type="submit" class="local-favorito-button">
                                <?= $favoritado ? '★ Remover dos favoritos' : '☆ Salvar nos favoritos' ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="local-favorito-login">
                            <i class="fa-solid fa-heart"></i>
                            Entre para salvar nos favoritos
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($local['descricao'])): ?>
                    <div class="local-descricao">
                        <h2>Sobre o local</h2>
                        <p><?= nl2br(htmlspecialchars($local['descricao'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="local-dados">
                    <?php if (!empty($local['endereco'])): ?>
                        <div class="local-dado">
                            <div class="local-dado-icone"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <span>Endereço</span>
                                <strong><?= htmlspecialchars($local['endereco']) ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($local['telefone'])): ?>
                        <div class="local-dado">
                            <div class="local-dado-icone"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <span>Telefone</span>
                                <strong><?= htmlspecialchars($local['telefone']) ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($local['email'])): ?>
                        <div class="local-dado">
                            <div class="local-dado-icone"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <span>E-mail</span>
                                <strong><?= htmlspecialchars($local['email']) ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($local['horario_funcionamento'])): ?>
                        <div class="local-dado">
                            <div class="local-dado-icone"><i class="fa-solid fa-clock"></i></div>
                            <div>
                                <span>Horário de funcionamento</span>
                                <strong><?= htmlspecialchars($local['horario_funcionamento']) ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($local['preco'] !== null && $local['preco'] !== ''): ?>
                        <div class="local-dado">
                            <div class="local-dado-icone"><i class="fa-solid fa-tag"></i></div>
                            <div>
                                <span>Preço</span>
                                <strong>R$ <?= number_format((float)$local['preco'], 2, ',', '.') ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($local['latitude']) && !empty($local['longitude'])): ?>
                    <div class="local-localizacao">
                        <div class="local-localizacao-header">
                            <div>
                                <span>LOCALIZAÇÃO</span>
                                <h2>Onde fica?</h2>
                            </div>
                            <a
                                href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($local['latitude'] . ',' . $local['longitude']) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="local-mapa-link"
                            >
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Abrir no Google Maps
                            </a>
                        </div>

                        <div
                            id="map"
                            data-lat="<?= htmlspecialchars($local['latitude']) ?>"
                            data-lng="<?= htmlspecialchars($local['longitude']) ?>"
                        ></div>

                        <div class="local-mapa-attribution">© OpenStreetMap contributors</div>
                    </div>
                <?php endif; ?>

                <div class="avaliacao-resumo">
                    <h2>Avaliações</h2>
                    <div class="avaliacao-media">
                        <span class="avaliacao-nota">
                            <?= number_format((float) $mediaAvaliacao['media'], 1, ',', '.') ?>
                        </span>
                        <span>baseado em <?= (int) $mediaAvaliacao['total'] ?> avaliação(ões)</span>
                    </div>
                </div>

                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <div class="avaliacao-form">
                        <h2>Deixe sua avaliação</h2>

                        <?php if ($mensagemAvaliacao !== null): ?>
                            <div class="avaliacao-mensagem <?= $avaliacaoSucesso ? 'sucesso' : 'erro' ?>">
                                <?= htmlspecialchars($mensagemAvaliacao) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="local.php?id=<?= (int) $id ?>">
                            <input type="hidden" name="acao_avaliacao" value="1">

                            <fieldset>
                                <legend>Nota</legend>
                                <div class="avaliacao-estrelas" role="radiogroup" aria-label="Selecione a nota do local">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <button
                                            type="button"
                                            class="avaliacao-estrela"
                                            data-nota="<?= $i ?>"
                                            aria-label="<?= $i ?> estrela<?= $i > 1 ? 's' : '' ?>"
                                            aria-pressed="false"
                                            title="<?= $i ?> estrela<?= $i > 1 ? 's' : '' ?>"
                                        >★</button>
                                    <?php endfor; ?>
                                    <input type="hidden" name="nota" id="nota-avaliacao" value="" required>
                                </div>
                            </fieldset>

                            <div>
                                <label for="comentario">Comentário (opcional)</label>
                                <textarea
                                    id="comentario"
                                    name="comentario"
                                    class="avaliacao-textarea"
                                    placeholder="Conte sua experiência neste local..."
                                ></textarea>
                            </div>

                            <button type="submit" class="avaliacao-botao">Enviar avaliação</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="avaliacao-form">
                        <h2>Deixe sua avaliação</h2>
                        <p>Faça <a href="login.php">login</a> para avaliar este local.</p>
                    </div>
                <?php endif; ?>

                <div class="avaliacao-bloco">
                    <h2>Comentários de visitantes</h2>

                    <?php if (empty($avaliacoes)): ?>
                        <p>Ainda não há avaliações para este local.</p>
                    <?php else: ?>
                        <div class="avaliacao-lista">
                            <?php foreach ($avaliacoes as $avaliacao): ?>
                                <article class="avaliacao-item">
                                    <div class="avaliacao-topo">
                                        <div class="avaliacao-autor">
                                            <?= htmlspecialchars($avaliacao['nome_usuario'] ?? 'Usuário') ?>
                                        </div>
                                        <div class="avaliacao-data">
                                            <?= date('d/m/Y', strtotime($avaliacao['data_avaliacao'])) ?>
                                        </div>
                                    </div>

                                    <div>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span style="color: <?= $i <= (int) ($avaliacao['nota'] ?? 0) ? '#f59e0b' : '#cbd5e1' ?>;">★</span>
                                        <?php endfor; ?>
                                    </div>

                                    <?php if (!empty($avaliacao['comentario'])): ?>
                                        <p class="avaliacao-comentario">
                                            <?= nl2br(htmlspecialchars($avaliacao['comentario'])) ?>
                                        </p>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </section>

    </div>

</main>

<footer class="footer">

    <div class="container">

        <div class="footer-brand">
            <h3>ArTour</h3>
            <span class="footer-slogan">Assistente Regional de Turismo</span>
            <p>Descubra novos lugares, cidades e experiências.</p>
        </div>

        <div class="footer-links">
            <a href="/Turismo-Lafa/index.php">Início</a>
            <a href="/Turismo-Lafa/index.php#categorias">Categorias</a>
            <a href="/Turismo-Lafa/index.php#locais">Locais</a>
            <a href="/Turismo-Lafa/index.php#eventos">Eventos</a>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© <?= date('Y') ?> ArTour. Todos os direitos reservados.</p>
    </div>

</footer>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const mapaElemento = document.getElementById('map');

    if (mapaElemento) {
        const latitude = parseFloat(mapaElemento.dataset.lat);
        const longitude = parseFloat(mapaElemento.dataset.lng);

        if (!isNaN(latitude) && !isNaN(longitude)) {
            const mapa = L.map('map').setView([latitude, longitude], 16);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
            }).addTo(mapa);

            const marcador = L.marker([latitude, longitude]).addTo(mapa);
            marcador.bindPopup('<strong><?= htmlspecialchars($local['nome'], ENT_QUOTES, 'UTF-8') ?></strong>').openPopup();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const estrelas = Array.from(document.querySelectorAll('.avaliacao-estrela'));
        const inputNota = document.getElementById('nota-avaliacao');

        if (!estrelas.length || !inputNota) {
            return;
        }

        function atualizarEstrelas(notaSelecionada) {
            estrelas.forEach((estrela) => {
                const valor = Number(estrela.dataset.nota);
                const ativa = valor <= notaSelecionada;
                estrela.classList.toggle('ativa', ativa);
                estrela.setAttribute('aria-pressed', ativa ? 'true' : 'false');
                estrela.setAttribute('aria-label', `${valor} estrela${valor > 1 ? 's' : ''}`);
            });
        }

        estrelas.forEach((estrela) => {
            estrela.addEventListener('click', function () {
                const notaSelecionada = Number(this.dataset.nota);
                inputNota.value = String(notaSelecionada);
                atualizarEstrelas(notaSelecionada);
            });

            estrela.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    this.click();
                }
            });
        });
    });
</script>
</body>

</html>
