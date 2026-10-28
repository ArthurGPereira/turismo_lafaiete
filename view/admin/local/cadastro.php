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
require_once '../../../controller/FotoController.php';

$localController = new LocalController();
$categoriaController = new CategoriaController();
$cidadeController = new CidadeController();
$fotoController = new FotoController();

$categorias = $categoriaController->listar();
$cidades = $cidadeController->listar();


/* =========================================================
   CADASTRO
========================================================= */

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

        'latitude' => $_POST['latitude'] !== ''
            ? $_POST['latitude']
            : null,

        'longitude' => $_POST['longitude'] !== ''
            ? $_POST['longitude']
            : null,

        'status' => 'ativo'
    ];

    $idLocal = $localController->cadastrar($dados);

    if (!$idLocal) {
        die('Erro ao cadastrar o local.');
    }


    /* =====================================================
       UPLOAD DAS FOTOS
    ====================================================== */

    if (
        isset($_FILES['fotos']) &&
        isset($_FILES['fotos']['name']) &&
        !empty($_FILES['fotos']['name'][0])
    ) {

        $tiposPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        $diretorio = __DIR__ . '/../../../uploads/fotos/';

        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $quantidadeFotos = count($_FILES['fotos']['name']);

        for ($i = 0; $i < $quantidadeFotos; $i++) {

            if (
                $_FILES['fotos']['error'][$i]
                !== UPLOAD_ERR_OK
            ) {
                continue;
            }

            $arquivoTemporario = $_FILES['fotos']['tmp_name'][$i];
            $tamanhoArquivo = $_FILES['fotos']['size'][$i];
            $nomeOriginal = $_FILES['fotos']['name'][$i];

            if ($tamanhoArquivo > 5 * 1024 * 1024) {
                die(
                    'A imagem "' .
                    htmlspecialchars($nomeOriginal) .
                    '" ultrapassa o limite de 5 MB.'
                );
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $tipoArquivo = finfo_file(
                $finfo,
                $arquivoTemporario
            );

            finfo_close($finfo);

            if (!isset($tiposPermitidos[$tipoArquivo])) {
                die(
                    'A imagem "' .
                    htmlspecialchars($nomeOriginal) .
                    '" possui um formato não permitido. ' .
                    'Use JPG, PNG ou WEBP.'
                );
            }

            $extensao = $tiposPermitidos[$tipoArquivo];

            $nomeArquivo =
                uniqid('local_', true) .
                '.' .
                $extensao;

            $caminhoArquivo =
                $diretorio .
                $nomeArquivo;

            if (
                !move_uploaded_file(
                    $arquivoTemporario,
                    $caminhoArquivo
                )
            ) {
                die(
                    'Erro ao salvar a imagem "' .
                    htmlspecialchars($nomeOriginal) .
                    '".'
                );
            }

            $caminhoBanco =
                'uploads/fotos/' .
                $nomeArquivo;

            $resultado = $fotoController->cadastrar([
                'id_local' => $idLocal,
                'caminho' => $caminhoBanco,
                'descricao' => 'Imagem do local'
            ]);

            if (!$resultado) {

                if (file_exists($caminhoArquivo)) {
                    unlink($caminhoArquivo);
                }

                die('Erro ao cadastrar a imagem no banco.');
            }
        }
    }


    /*
     * Depois do cadastro, abre a edição do local.
     * Assim o administrador pode conferir os dados
     * e também a localização cadastrada.
     */
    header(
        'Location: editar.php?id=' .
        (int) $idLocal
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

    <title>Cadastrar Local - ArTour</title>


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
                <span>Sair da conta</span>
            </a>

        </div>

    </aside>


    <main class="admin-content">

        <header class="admin-header">

            <h2>
                <i class="fa-solid fa-location-plus"></i>
                Cadastrar local
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

                <h1>Novo local</h1>

                <p>
                    Cadastre um novo local no ArTour.
                </p>

            </div>


            <div class="admin-section">

                <div class="admin-section-header">

                    <div>

                        <h3>
                            <i class="fa-solid fa-plus"></i>
                            Informações do local
                        </h3>

                        <p>
                            Preencha os dados abaixo.
                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="form-group">

                        <label for="nome">
                            Nome
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="cidade">
                            Cidade
                        </label>

                        <select
                            id="cidade"
                            name="id_cidade"
                            required
                        >

                            <option value="">
                                Selecione uma cidade
                            </option>

                            <?php foreach ($cidades as $cidade): ?>

                                <option
                                    value="<?= (int) $cidade['id_cidade'] ?>"
                                    data-nome="<?= htmlspecialchars($cidade['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-estado="<?= htmlspecialchars($cidade['estado'], ENT_QUOTES, 'UTF-8') ?>"
                                >

                                    <?= htmlspecialchars(
                                        $cidade['nome']
                                    ) ?>

                                    -

                                    <?= htmlspecialchars(
                                        $cidade['estado']
                                    ) ?>

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
                        ></textarea>

                    </div>


                    <div class="form-group">

                        <label for="fotos">

                            <i class="fa-solid fa-images"></i>

                            Imagens do local

                        </label>

                        <input
                            type="file"
                            id="fotos"
                            name="fotos[]"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                        >

                        <small>
                            JPG, PNG ou WEBP.
                            Máximo de 5 MB por imagem.
                        </small>

                    </div>


                    <div class="form-group">

                        <label for="endereco">
                            Endereço
                        </label>

                        <input
                            type="text"
                            id="endereco"
                            name="endereco"
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
                        >

                    </div>


                    <!-- =================================================
                         LOCALIZAÇÃO
                    ================================================== -->

                    <div class="form-group local-mapa-admin">

                        <div class="local-mapa-topo">
                            <div>
                                <span class="local-mapa-label">LOCALIZAÇÃO</span>
                                <h3>Localização no mapa</h3>
                                <p>Digite o endereço e selecione a cidade. O mapa buscará a localização automaticamente.</p>
                            </div>
                        </div>

                        <div id="mapa-local"></div>

                        <div id="mensagem-mapa" class="local-mensagem-mapa">
                            <i class="fa-solid fa-circle-info"></i>
                            Selecione uma cidade e informe o endereço para localizar.
                        </div>

                        <div class="local-coordenadas">
                            <div class="local-coordenada">
                                <span>LATITUDE</span>
                                <strong id="valor-latitude">Não definida</strong>
                            </div>
                            <div class="local-coordenada">
                                <span>LONGITUDE</span>
                                <strong id="valor-longitude">Não definida</strong>
                            </div>
                        </div>

                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">

                    </div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            name="cadastrar_local"
                            class="btn"
                        >

                            <i class="fa-solid fa-check"></i>

                            Cadastrar local

                        </button>


                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >

                            <i class="fa-solid fa-arrow-left"></i>

                            Voltar

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
    const campoEndereco = document.getElementById('endereco');
    const campoCidade = document.getElementById('cidade');
    const campoLatitude = document.getElementById('latitude');
    const campoLongitude = document.getElementById('longitude');
    const valorLatitude = document.getElementById('valor-latitude');
    const valorLongitude = document.getElementById('valor-longitude');
    const mensagemMapa = document.getElementById('mensagem-mapa');

    if (!campoEndereco || !campoCidade || !campoLatitude || !campoLongitude) return;

    const mapa = L.map('mapa-local', { zoomControl: false }).setView([-15.7801, -47.9292], 5);
    L.control.zoom({ position: 'bottomright' }).addTo(mapa);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapa);

    const iconeArTour = L.divIcon({
        className: 'artour-marker-wrapper',
        html: '<div class="artour-map-marker"><i class="fa-solid fa-location-dot"></i></div>',
        iconSize: [42, 48],
        iconAnchor: [21, 48],
        popupAnchor: [0, -44]
    });

    let marcador = null;
    let ultimaConsulta = '';

    function mensagem(texto, icone = 'fa-circle-info') {
        mensagemMapa.innerHTML = '<i class="fa-solid ' + icone + '"></i> ' + texto;
    }

    function atualizarCoordenadas(lat, lng) {
        lat = Number(lat);
        lng = Number(lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
        campoLatitude.value = lat.toFixed(8);
        campoLongitude.value = lng.toFixed(8);
        valorLatitude.textContent = lat.toFixed(8);
        valorLongitude.textContent = lng.toFixed(8);
    }

    function posicionarMarcador(lat, lng, zoom = 17) {
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
        mapa.setView([lat, lng], zoom);
        if (marcador) {
            marcador.setLatLng([lat, lng]);
        } else {
            marcador = L.marker([lat, lng], { draggable: true, icon: iconeArTour }).addTo(mapa);
            marcador.on('dragend', function () {
                const p = marcador.getLatLng();
                atualizarCoordenadas(p.lat, p.lng);
                mensagem('Localização ajustada pelo marcador.', 'fa-circle-check');
            });
        }
        atualizarCoordenadas(lat, lng);
    }

    async function localizarEndereco() {
        const endereco = campoEndereco.value.trim();
        const opcao = campoCidade.options[campoCidade.selectedIndex];

        if (!endereco || !opcao || !opcao.value) {
            mensagem('Informe o endereço e selecione uma cidade.');
            return;
        }

        const cidade = opcao.dataset.nome || '';
        const estado = opcao.dataset.estado || '';
        const consulta = `${endereco}, ${cidade}, ${estado}, Brasil`;

        if (consulta === ultimaConsulta) return;
        ultimaConsulta = consulta;

        mensagem('Procurando o endereço no mapa...', 'fa-spinner fa-spin');

        try {
            const resposta = await fetch(
                'geocodificar.php?q=' + encodeURIComponent(consulta),
                { headers: { 'Accept': 'application/json' } }
            );
            if (!resposta.ok) throw new Error('Falha na consulta');
            const dados = await resposta.json();

            if (!dados.sucesso) {
                ultimaConsulta = '';
                mensagem(dados.mensagem || 'Endereço não encontrado. Tente informar rua e número.', 'fa-triangle-exclamation');
                return;
            }

            const lat = parseFloat(dados.latitude);
            const lng = parseFloat(dados.longitude);
            posicionarMarcador(lat, lng);
            marcador.bindPopup('<strong>Local encontrado</strong>').openPopup();
            mensagem('Endereço localizado. Você pode arrastar o marcador ou clicar no mapa para ajustar.', 'fa-circle-check');
        } catch (erro) {
            console.error(erro);
            ultimaConsulta = '';
            mensagem('Não foi possível consultar o mapa. Confira sua conexão e tente novamente.', 'fa-triangle-exclamation');
        }
    }

    campoEndereco.addEventListener('change', localizarEndereco);
    campoEndereco.addEventListener('blur', localizarEndereco);
    campoCidade.addEventListener('change', function () {
        ultimaConsulta = '';
        localizarEndereco();
    });

    mapa.on('click', function (evento) {
        posicionarMarcador(evento.latlng.lat, evento.latlng.lng);
        mensagem('Localização ajustada manualmente.', 'fa-circle-check');
    });

    // Evita o problema do Leaflet calcular o tamanho antes do layout terminar.
    setTimeout(function () { mapa.invalidateSize(); }, 250);
});
</script>

</body>
</html>
