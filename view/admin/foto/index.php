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
   CADASTRAR LOCAL
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
        'preco' => $_POST['preco'] !== ''
            ? $_POST['preco']
            : null,
        'latitude' => null,
        'longitude' => null,
        'status' => 'ativo'
    ];


    /*
     * Cadastra o local primeiro.
     * Precisamos do ID gerado para relacionar
     * as imagens com esse local.
     */
    $idLocal = $localController->cadastrar($dados);


    if (!$idLocal) {
        die('Erro ao cadastrar o local.');
    }


    /* =====================================================
       CADASTRAR VÁRIAS FOTOS
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


        /*
         * Cria a pasta caso ela ainda não exista.
         */
        if (!is_dir($diretorio)) {

            mkdir(
                $diretorio,
                0755,
                true
            );
        }


        $quantidadeFotos =
            count($_FILES['fotos']['name']);


        /*
         * Percorre todas as imagens selecionadas.
         */
        for ($i = 0; $i < $quantidadeFotos; $i++) {

            /*
             * Ignora arquivos que apresentaram erro.
             */
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


            /*
             * Limite de 5 MB por imagem.
             */
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


            /*
             * Descobre o tipo real do arquivo.
             */
            $finfo =
                finfo_open(FILEINFO_MIME_TYPE);


            $tipoArquivo =
                finfo_file(
                    $finfo,
                    $arquivoTemporario
                );


            finfo_close($finfo);


            /*
             * Verifica se o formato é permitido.
             */
            if (
                !isset(
                    $tiposPermitidos[$tipoArquivo]
                )
            ) {

                die(
                    'A imagem "' .
                    htmlspecialchars($nomeOriginal) .
                    '" possui um formato não permitido. ' .
                    'Use JPG, PNG ou WEBP.'
                );
            }


            $extensao =
                $tiposPermitidos[$tipoArquivo];


            /*
             * Cria um nome único para a imagem.
             */
            $nomeArquivo =
                uniqid(
                    'local_',
                    true
                ) .
                '.' .
                $extensao;


            /*
             * Caminho físico onde a imagem será salva.
             */
            $caminhoArquivo =
                $diretorio .
                $nomeArquivo;


            /*
             * Move a imagem para a pasta uploads/fotos.
             */
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


            /*
             * Caminho que será armazenado no banco.
             */
            $caminhoBanco =
                'uploads/fotos/' .
                $nomeArquivo;


            /*
             * Relaciona a foto ao local.
             */
            $resultado =
                $fotoController->cadastrar([
                    'id_local' => $idLocal,
                    'caminho' => $caminhoBanco,
                    'descricao' => 'Imagem do local'
                ]);


            /*
             * Se ocorrer erro no banco,
             * remove a imagem que acabou de ser salva.
             */
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
    }


    /*
     * Depois de cadastrar o local e as imagens,
     * volta para a lista de locais.
     */
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

    <title>
        Cadastrar Local - Tour Local
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

            <h1>Tour Local</h1>

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
                href="index.php"
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


        <!-- HEADER -->

        <header class="admin-header">


            <h2>

                <i class="fa-solid fa-location-dot"></i>

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



        <!-- CONTEÚDO PRINCIPAL -->

        <section class="admin-main">


            <div class="admin-welcome">


                <h1>
                    Novo local
                </h1>


                <p>
                    Cadastre um novo local no Tour Local.
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


                    <!-- NOME -->

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



                    <!-- CIDADE -->

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


                            <?php foreach (
                                $cidades
                                as $cidade
                            ): ?>

                                <option
                                    value="<?= $cidade['id_cidade'] ?>"
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



                    <!-- CATEGORIA -->

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


                            <?php foreach (
                                $categorias
                                as $categoria
                            ): ?>

                                <option
                                    value="<?= $categoria['id_categoria'] ?>"
                                >

                                    <?= htmlspecialchars(
                                        $categoria['nome']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>


                        </select>

                    </div>



                    <!-- DESCRIÇÃO -->

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



                    <!-- FOTOS -->

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

                            Você pode selecionar várias
                            imagens de uma vez.

                            <br>

                            Formatos permitidos:
                            JPG, PNG ou WEBP.

                            <br>

                            Máximo de 5 MB por imagem.

                        </small>


                    </div>



                    <!-- ENDEREÇO -->

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



                    <!-- TELEFONE -->

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



                    <!-- EMAIL -->

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



                    <!-- HORÁRIO -->

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



                    <!-- PREÇO -->

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



                    <!-- BOTÕES -->

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


</body>

</html>
