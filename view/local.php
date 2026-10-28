<?php

require_once '../controller/LocalController.php';
require_once '../controller/FotoController.php';

$localController = new LocalController();
$fotoController = new FotoController();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../index.php');
    exit;
}

$local = $localController->buscarPorId($id);

if (!$local) {
    header('Location: ../index.php');
    exit;
}

$fotos = $fotoController->listarPorLocal($id);

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


                <?php if (!empty($local['descricao'])): ?>

                    <div class="local-descricao">

                        <h2>
                            Sobre o local
                        </h2>

                        <p>
                            <?= nl2br(htmlspecialchars($local['descricao'])) ?>
                        </p>

                    </div>

                <?php endif; ?>


                <div class="local-dados">

                    <?php if (!empty($local['endereco'])): ?>

                        <div class="local-dado">

                            <div class="local-dado-icone">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>

                            <div>

                                <span>
                                    Endereço
                                </span>

                                <strong>
                                    <?= htmlspecialchars($local['endereco']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endif; ?>


                    <?php if (!empty($local['telefone'])): ?>

                        <div class="local-dado">

                            <div class="local-dado-icone">
                                <i class="fa-solid fa-phone"></i>
                            </div>

                            <div>

                                <span>
                                    Telefone
                                </span>

                                <strong>
                                    <?= htmlspecialchars($local['telefone']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endif; ?>


                    <?php if (!empty($local['email'])): ?>

                        <div class="local-dado">

                            <div class="local-dado-icone">
                                <i class="fa-solid fa-envelope"></i>
                            </div>

                            <div>

                                <span>
                                    E-mail
                                </span>

                                <strong>
                                    <?= htmlspecialchars($local['email']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endif; ?>


                    <?php if (!empty($local['horario_funcionamento'])): ?>

                        <div class="local-dado">

                            <div class="local-dado-icone">
                                <i class="fa-solid fa-clock"></i>
                            </div>

                            <div>

                                <span>
                                    Horário de funcionamento
                                </span>

                                <strong>
                                    <?= htmlspecialchars($local['horario_funcionamento']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endif; ?>


                    <?php if ($local['preco'] !== null && $local['preco'] !== ''): ?>

                        <div class="local-dado">

                            <div class="local-dado-icone">
                                <i class="fa-solid fa-tag"></i>
                            </div>

                            <div>

                                <span>
                                    Preço
                                </span>

                                <strong>
                                    R$ <?= number_format((float)$local['preco'], 2, ',', '.') ?>
                                </strong>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>


                <?php if (!empty($local['latitude']) && !empty($local['longitude'])): ?>

    <div class="local-localizacao">

        <div class="local-localizacao-header">

            <div>
                <span>LOCALIZAÇÃO</span>

                <h2>
                    Onde fica?
                </h2>
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

        <div class="local-mapa-attribution">
            © OpenStreetMap contributors
        </div>

    </div>

<?php endif; ?>

            </div>

        </section>

    </div>

</main>


<footer class="footer">

    <div class="container">

        <div class="footer-brand">

            <h3>
                ArTour
            </h3>

            <span class="footer-slogan">
                Assistente Regional de Turismo
            </span>

            <p>
                Descubra novos lugares, cidades e experiências.
            </p>

        </div>


        <div class="footer-links">

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

        </div>

    </div>


    <div class="footer-bottom">

        <p>
            © <?= date('Y') ?> ArTour.
            Todos os direitos reservados.
        </p>

    </div>

</footer>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const mapaElemento = document.getElementById('map');

    if (mapaElemento) {

        const latitude = parseFloat(
            mapaElemento.dataset.lat
        );

        const longitude = parseFloat(
            mapaElemento.dataset.lng
        );

        if (!isNaN(latitude) && !isNaN(longitude)) {

            const mapa = L.map('map').setView(
                [latitude, longitude],
                16
            );

            L.tileLayer(
                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
                }
            ).addTo(mapa);

            const marcador = L.marker([
                latitude,
                longitude
            ]).addTo(mapa);

            marcador
                .bindPopup(
                    '<strong><?= htmlspecialchars($local['nome'], ENT_QUOTES, 'UTF-8') ?></strong>'
                )
                .openPopup();
        }
    }
</script>
</body>

</html>
