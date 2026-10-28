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
require_once '../../../controller/FotoController.php';

$localController = new LocalController();
$fotoController = new FotoController();


/* =========================================================
   ID DO LOCAL
========================================================= */

$idLocal = filter_input(
    INPUT_GET,
    'id_local',
    FILTER_VALIDATE_INT
);


if (!$idLocal) {

    header(
        'Location: ../local/index.php'
    );

    exit;
}


/* =========================================================
   BUSCAR LOCAL
========================================================= */

$local =
    $localController->buscarPorId($idLocal);


if (!$local) {

    echo 'Local não encontrado.';

    exit;
}


/* =========================================================
   EXCLUIR FOTO
========================================================= */

if (isset($_GET['excluir'])) {

    $idFoto = filter_input(
        INPUT_GET,
        'excluir',
        FILTER_VALIDATE_INT
    );


    if ($idFoto) {

        $fotoController->excluir($idFoto);

    }


    header(
        'Location: index.php?id_local=' .
        $idLocal
    );

    exit;
}


/* =========================================================
   FOTOS DO LOCAL
========================================================= */

$fotos =
    $fotoController->listarPorLocal(
        $idLocal
    );


/* =========================================================
   UPLOAD DE FOTOS
========================================================= */

if (isset($_POST['cadastrar_fotos'])) {

    if (
        !isset($_FILES['fotos']) ||
        empty($_FILES['fotos']['name'][0])
    ) {

        header(
            'Location: index.php?id_local=' .
            $idLocal
        );

        exit;
    }


    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];


    $diretorio =
        __DIR__ .
        '/../../../uploads/fotos/';


    if (!is_dir($diretorio)) {

        mkdir(
            $diretorio,
            0755,
            true
        );
    }


    $quantidadeFotos =
        count(
            $_FILES['fotos']['name']
        );


    for (
        $i = 0;
        $i < $quantidadeFotos;
        $i++
    ) {

        if (
            $_FILES['fotos']['error'][$i]
            !== UPLOAD_ERR_OK
        ) {
            continue;
        }


        $arquivoTemporario =
            $_FILES['fotos']['tmp_name'][$i];


        $tamanhoArquivo =
            $_FILES['fotos']['size'][$i];


        $nomeOriginal =
            $_FILES['fotos']['name'][$i];


        if (
            $tamanhoArquivo >
            5 * 1024 * 1024
        ) {

            die(
                'A imagem "' .
                htmlspecialchars($nomeOriginal) .
                '" ultrapassa o limite de 5 MB.'
            );
        }


        $finfo =
            finfo_open(
                FILEINFO_MIME_TYPE
            );


        $tipoArquivo =
            finfo_file(
                $finfo,
                $arquivoTemporario
            );


        finfo_close($finfo);


        if (
            !isset(
                $tiposPermitidos[$tipoArquivo]
            )
        ) {

            die(
                'A imagem "' .
                htmlspecialchars($nomeOriginal) .
                '" possui um formato não permitido.'
            );
        }


        $extensao =
            $tiposPermitidos[$tipoArquivo];


        $nomeArquivo =
            uniqid(
                'local_',
                true
            ) .
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


        $resultado =
            $fotoController->cadastrar([
                'id_local' => $idLocal,
                'caminho' => $caminhoBanco,
                'descricao' => 'Imagem do local'
            ]);


        if (!$resultado) {

            if (
                file_exists(
                    $caminhoArquivo
                )
            ) {

                unlink(
                    $caminhoArquivo
                );
            }

            die(
                'Erro ao cadastrar a imagem no banco.'
            );
        }
    }


    header(
        'Location: index.php?id_local=' .
        $idLocal
    );

    exit;
}


$fotos =
    $fotoController->listarPorLocal(
        $idLocal
    );

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
        Fotos - <?= htmlspecialchars($local['nome']) ?> - ArTour
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
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="admin-page">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="admin-sidebar">

        <div class="admin-brand">

            <h1>
                ArTour
            </h1>

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

                <span>
                    Dashboard
                </span>

            </a>


            <a
                href="../local/index.php"
                class="active"
            >

                <i class="fa-solid fa-location-dot"></i>

                <span>
                    Locais
                </span>

            </a>


            <a href="../categoria/index.php">

                <i class="fa-solid fa-layer-group"></i>

                <span>
                    Categorias
                </span>

            </a>


            <a href="../cidade/index.php">

                <i class="fa-solid fa-city"></i>

                <span>
                    Cidades
                </span>

            </a>


            <a href="../usuario/index.php">

                <i class="fa-solid fa-users"></i>

                <span>
                    Usuários
                </span>

            </a>


            <div class="admin-menu-title">
                Sistema
            </div>


            <a href="../../../index.php">

                <i class="fa-solid fa-house"></i>

                <span>
                    Ver página inicial
                </span>

            </a>


            <a href="../../perfil.php">

                <i class="fa-solid fa-user"></i>

                <span>
                    Meu perfil
                </span>

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


    <!-- =====================================================
         CONTEÚDO
    ====================================================== -->

    <main class="admin-content">


        <header class="admin-header">

            <h2>

                <i class="fa-solid fa-images"></i>

                Fotos do local

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

                    <?= htmlspecialchars(
                        $local['nome']
                    ) ?>

                </h1>


                <p>

                    Gerencie as imagens deste local.

                </p>

            </div>


            <!-- =================================================
                 AÇÕES
            ================================================== -->

            <div class="admin-section">

                <div class="admin-section-header">

                    <div>

                        <h3>

                            <i class="fa-solid fa-upload"></i>

                            Adicionar imagens

                        </h3>

                        <p>

                            Selecione uma ou várias imagens.

                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="form-group">

                        <label for="fotos">

                            <i class="fa-solid fa-images"></i>

                            Imagens

                        </label>


                        <input
                            type="file"
                            id="fotos"
                            name="fotos[]"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                            required
                        >


                        <small>

                            JPG, PNG ou WEBP.
                            Máximo de 5 MB por imagem.

                        </small>

                    </div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            name="cadastrar_fotos"
                            class="btn"
                        >

                            <i class="fa-solid fa-upload"></i>

                            Enviar imagens

                        </button>


                        <a
                            href="../local/editar.php?id=<?= $idLocal ?>"
                            class="btn btn-secondary"
                        >

                            <i class="fa-solid fa-arrow-left"></i>

                            Voltar para o local

                        </a>

                    </div>

                </form>

            </div>


            <!-- =================================================
                 GALERIA
            ================================================== -->

            <div class="admin-section">

                <div class="admin-section-header">

                    <div>

                        <h3>

                            <i class="fa-solid fa-photo-film"></i>

                            Imagens cadastradas

                        </h3>

                        <p>

                            <?= count($fotos) ?>
                            imagem(ns) cadastrada(s).

                        </p>

                    </div>

                </div>


                <?php if (!empty($fotos)): ?>

                    <div class="local-fotos-admin-grid">

                        <?php foreach ($fotos as $foto): ?>

                            <article class="local-foto-admin-card">


                                <img
                                    src="../../../<?= htmlspecialchars($foto['caminho']) ?>"
                                    alt="Imagem de <?= htmlspecialchars($local['nome']) ?>"
                                >


                                <div class="local-foto-admin-footer">

                                    <span>

                                        <i class="fa-solid fa-image"></i>

                                        Imagem do local

                                    </span>


                                    <a
                                        href="index.php?id_local=<?= $idLocal ?>&excluir=<?= (int) $foto['id_foto'] ?>"
                                        class="local-foto-excluir"
                                        onclick="return confirm('Deseja realmente excluir esta imagem?');"
                                        title="Excluir imagem"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                    </a>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="local-fotos-admin-vazio">

                        <i class="fa-regular fa-images"></i>

                        <h3>
                            Nenhuma imagem cadastrada
                        </h3>

                        <p>
                            Adicione imagens usando o formulário acima.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>
</html>
