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


/* =========================================================
   ID DO LOCAL
========================================================= */

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

$local = $localController->buscarPorId($id);

if (!$local) {
    echo 'Local não encontrado.';
    exit;
}


/* =========================================================
   EDITAR LOCAL
========================================================= */

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
        'preco' => $_POST['preco'] !== ''
            ? $_POST['preco']
            : null,

        'latitude' => $_POST['latitude'] !== ''
            ? $_POST['latitude']
            : null,

        'longitude' => $_POST['longitude'] !== ''
            ? $_POST['longitude']
            : null,

        'status' => $local['status'] ?? 'ativo'
    ];


    $resultado =
        $localController->editar($dados);


    if (!$resultado) {
        die('Erro ao atualizar o local.');
    }


    header(
        'Location: editar.php?id=' .
        $id
    );

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

    <title>
        Editar Local - ArTour
    </title>


    <link
        rel="stylesheet"
        href="../../../assets/css/global.css"
    >

    <link
        rel="stylesheet"
        href="../../../assets/css/admin.css"
    >

    <link
        rel="stylesheet"
        href="../../../assets/css/local.css"
    >

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="admin-page">

    <aside class="admin-sidebar">

        <div class="admin-brand">

            <h1>ArTour</h1>

            <span>
                Painel administrativo
            </span>

        </div>


        <nav class="admin-menu">

            <div class="admin-menu-title">
                Gerenciamento
            </div>

            <a href="../index.php">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a href="index.php" class="active">
                <i class="fa-solid fa-location-dot"></i>
                <span>Locais</span>
            </a>

            <a href="../categoria/index.php">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categorias</span>
            </a>

            <a href="../cidade/index.php">
                <i class="fa-solid fa-city"></i>
                <span>Cidades</span>
            </a>

            <a href="../usuario/index.php">
                <i class="fa-solid fa-users"></i>
                <span>Usuários</span>
            </a>


            <div class="admin-menu-title">
                Sistema
            </div>

            <a href="../../../index.php">
                <i class="fa-solid fa-house"></i>
                <span>Ver página inicial</span>
            </a>

            <a href="../../perfil.php">
                <i class="fa-solid fa-user"></i>
                <span>Meu perfil</span>
            </a>

        </nav>


        <div class="admin-sidebar-footer">

            <a href="../../logout.php">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>
                    Sair da conta
                </span>

            </a>

        </div>

    </aside>


    <main class="admin-content">

        <header class="admin-header">

            <h2>

                <i class="fa-solid fa-pen-to-square"></i>

                Editar local

            </h2>


            <div class="admin-user">

                <div class="admin-user-icon">

                    <?= strtoupper(
                        substr(
                            $_SESSION['nome_usuario'],
                            0,
                            1
                        )
                    ) ?>

                </div>

                <span>

                    <?= htmlspecialchars(
                        $_SESSION['nome_usuario']
                    ) ?>

                </span>

            </div>

        </header>


        <section class="admin-main">

            <div class="admin-welcome">

                <h1>
                    Editar local
                </h1>

                <p>
                    Atualize as informações de
                    <?= htmlspecialchars($local['nome']) ?>.
                </p>

            </div>


            <div class="admin-section">

                <div class="admin-section-header">

                    <div>

                        <h3>

                            <i class="fa-solid fa-location-dot"></i>

                            Informações do local

                        </h3>

                        <p>
                            Altere os dados necessários.
                        </p>

                    </div>

                </div>


                <form method="POST">


                    <div class="form-group">

                        <label for="nome">
                            Nome
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            value="<?= htmlspecialchars($local['nome']) ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="cidade">
                            Cidade
                        </label>

                        <select
    name="id_cidade"
    id="id_cidade"
    required
>

    <option value="">
        Selecione uma cidade
    </option>

    <?php foreach ($cidades as $cidade): ?>

        <option
            value="<?= (int) $cidade['id_cidade'] ?>"
            data-nome="<?= htmlspecialchars($cidade['nome']) ?>"
            data-estado="<?= htmlspecialchars($cidade['estado']) ?>"
            <?= (
                isset($local['id_cidade']) &&
                (int) $local['id_cidade'] === (int) $cidade['id_cidade']
            ) ? 'selected' : '' ?>
        >

            <?= htmlspecialchars($cidade['nome']) ?>
            -
            <?= htmlspecialchars($cidade['estado']) ?>

        </option>

    <?php endforeach; ?>

</select>

                    </div>


                    <div class="form-group">

                        <label for="categoria">
                            Categoria
                        </label>

                        <select
                            id="categoria"
                            name="id_categoria"
                            required
                        >

                            <option value="">
                                Selecione uma categoria
                            </option>

                            <?php foreach ($categorias as $categoria): ?>

                                <option
                                    value="<?= (int) $categoria['id_categoria'] ?>"
                                    <?= (int) $local['id_categoria'] === (int) $categoria['id_categoria']
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $categoria['nome']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="descricao">
                            Descrição
                        </label>

                        <textarea
                            id="descricao"
                            name="descricao"
                            rows="5"
                        ><?= htmlspecialchars($local['descricao'] ?? '') ?></textarea>

                    </div>


                    <div class="form-group">

                        <label for="endereco">
                            Endereço
                        </label>

                        <input
                            type="text"
                            id="endereco"
                            name="endereco"
                            value="<?= htmlspecialchars($local['endereco']) ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="telefone">
                            Telefone
                        </label>

                        <input
                            type="text"
                            id="telefone"
                            name="telefone"
                            value="<?= htmlspecialchars($local['telefone'] ?? '') ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="email">
                            E-mail
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($local['email'] ?? '') ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="horario_funcionamento">
                            Horário de funcionamento
                        </label>

                        <input
                            type="text"
                            id="horario_funcionamento"
                            name="horario_funcionamento"
                            value="<?= htmlspecialchars($local['horario_funcionamento'] ?? '') ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="preco">
                            Preço
                        </label>

                        <input
                            type="number"
                            id="preco"
                            name="preco"
                            step="0.01"
                            min="0"
                            value="<?= htmlspecialchars($local['preco'] ?? '') ?>"
                        >

                    </div>


                    <!-- =================================================
                         MAPA
                    ================================================== -->

                    <!-- =================================================
     MAPA
================================================== -->

<div class="form-group local-mapa-admin">

    <div class="local-mapa-topo">

        <div>

            <span class="local-mapa-label">
                LOCALIZAÇÃO
            </span>

            <h3>
                Localização no mapa
            </h3>

            <p>
                Use o endereço cadastrado para encontrar o local automaticamente.
            </p>

        </div>

        <span class="local-mapa-ajuda">Atualiza ao alterar endereço ou cidade</span>

    </div>

    <div
        id="mapa-local"
        data-lat="<?= htmlspecialchars($local['latitude'] ?? '') ?>"
        data-lng="<?= htmlspecialchars($local['longitude'] ?? '') ?>"
    ></div>

    <div
        id="mensagem-mapa"
        class="local-mensagem-mapa"
    >
        <i class="fa-solid fa-circle-info"></i>

        Você pode arrastar o marcador ou clicar no mapa para ajustar a localização.

    </div>

    <div class="local-coordenadas">

        <div class="local-coordenada">

            <span>LATITUDE</span>

            <strong id="valor-latitude">
                <?= htmlspecialchars($local['latitude'] ?? 'Não definida') ?>
            </strong>

        </div>

        <div class="local-coordenada">

            <span>LONGITUDE</span>

            <strong id="valor-longitude">
                <?= htmlspecialchars($local['longitude'] ?? 'Não definida') ?>
            </strong>

        </div>

    </div>

    <input
        type="hidden"
        id="latitude"
        name="latitude"
        value="<?= htmlspecialchars($local['latitude'] ?? '') ?>"
    >

    <input
        type="hidden"
        id="longitude"
        name="longitude"
        value="<?= htmlspecialchars($local['longitude'] ?? '') ?>"
    >

</div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            name="editar_local"
                            class="btn"
                        >

                            <i class="fa-solid fa-check"></i>

                            Salvar alterações

                        </button>


                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >

                            <i class="fa-solid fa-arrow-left"></i>

                            Voltar

                        </a>


                        <a
                            href="../foto/index.php?id_local=<?= $id ?>"
                            class="btn btn-secondary"
                        >

                            <i class="fa-solid fa-images"></i>

                            Gerenciar fotos

                        </a>

                    </div>

                </form>

            </div>

        </section>

    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('mapa-local');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const latText = document.getElementById('valor-latitude');
    const lngText = document.getElementById('valor-longitude');
    const msg = document.getElementById('mensagem-mapa');
    const enderecoInput = document.querySelector('[name="endereco"]');
    const cidadeSelect = document.querySelector('[name="id_cidade"]');
    if (!el || !latInput || !lngInput || !msg || !enderecoInput || !cidadeSelect) return;

    const savedLat = parseFloat(el.dataset.lat);
    const savedLng = parseFloat(el.dataset.lng);
    const hasSavedCoords = Number.isFinite(savedLat) && Number.isFinite(savedLng);
    const map = L.map('mapa-local', { zoomControl: false }).setView(
        hasSavedCoords ? [savedLat, savedLng] : [-15.7801, -47.9292],
        hasSavedCoords ? 16 : 5
    );
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const artourIcon = L.divIcon({
        className: 'artour-marker-wrapper',
        html: '<div class="artour-map-marker"><i class="fa-solid fa-location-dot"></i></div>',
        iconSize: [42, 48], iconAnchor: [21, 48], popupAnchor: [0, -44]
    });
    let marker = null;
    let timer = null;
    let lastQuery = '';

    function setMessage(text, icon = 'fa-circle-info') {
        msg.innerHTML = '<i class="fa-solid ' + icon + '"></i> ' + text;
    }
    function updateCoords(lat, lng) {
        lat = Number(lat); lng = Number(lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        if (latText) latText.textContent = lat.toFixed(8);
        if (lngText) lngText.textContent = lng.toFixed(8);
    }
    function placeMarker(lat, lng, zoom = 17) {
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
        map.setView([lat, lng], zoom);
        if (!marker) {
            marker = L.marker([lat, lng], { draggable: true, icon: artourIcon }).addTo(map);
            marker.on('dragend', function () {
                const p = marker.getLatLng();
                updateCoords(p.lat, p.lng);
                setMessage('Localização ajustada pelo marcador.', 'fa-circle-check');
            });
        } else marker.setLatLng([lat, lng]);
        updateCoords(lat, lng);
    }
    async function geocodeAddress() {
        const address = enderecoInput.value.trim();
        const option = cidadeSelect.options[cidadeSelect.selectedIndex];
        if (!address || !option || !option.value) {
            setMessage('Informe o endereço e selecione uma cidade.');
            return;
        }
        const city = option.dataset.nome || option.textContent.trim();
        const state = option.dataset.estado || '';
        const query = `${address}, ${city}, ${state}, Brasil`;
        if (query === lastQuery) return;
        lastQuery = query;
        setMessage('Procurando o endereço no mapa...', 'fa-spinner fa-spin');
        try {
            const response = await fetch('geocodificar.php?q=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Falha na consulta');
            const data = await response.json();
            if (!data.sucesso) {
                lastQuery = '';
                setMessage(data.mensagem || 'Endereço não encontrado. Confira o endereço informado.', 'fa-triangle-exclamation');
                return;
            }
            placeMarker(parseFloat(data.latitude), parseFloat(data.longitude));
            marker.bindPopup('<strong>Endereço encontrado</strong>').openPopup();
            setMessage('Endereço localizado. Arraste o marcador ou clique no mapa para ajustar.', 'fa-circle-check');
        } catch (error) {
            console.error(error);
            lastQuery = '';
            setMessage('Não foi possível consultar o mapa. Confira sua conexão.', 'fa-triangle-exclamation');
        }
    }
    function scheduleGeocode() {
        clearTimeout(timer);
        lastQuery = '';
        timer = setTimeout(geocodeAddress, 700);
    }

    if (hasSavedCoords) {
        placeMarker(savedLat, savedLng, 16);
        setMessage('Localização carregada do banco de dados.', 'fa-circle-check');
    } else {
        setTimeout(geocodeAddress, 400);
    }

    enderecoInput.addEventListener('blur', geocodeAddress);
    cidadeSelect.addEventListener('change', function () {
        lastQuery = '';
        geocodeAddress();
    });
    enderecoInput.addEventListener('input', scheduleGeocode);

    map.on('click', function (event) {
        placeMarker(event.latlng.lat, event.latlng.lng);
        setMessage('Localização ajustada manualmente.', 'fa-circle-check');
    });
    setTimeout(function () { map.invalidateSize(); }, 250);
});
</script>
</body>
</html>

</body>
</html>
