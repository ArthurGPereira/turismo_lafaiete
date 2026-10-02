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
   CADASTRO DO LOCAL
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
     * Primeiro cadastra o local.
     * Assim conseguimos pegar o ID dele
     * para relacionar as fotos.
     */
    $idLocal = $localController->cadastrar($dados);


    if (!$idLocal) {
        die('Erro ao cadastrar o local.');
    }


    /* =====================================================
       UPLOAD DE VÁRIAS FOTOS
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


        /*
         * Quantidade de arquivos enviados.
         */
        $quantidadeFotos =
            count($_FILES['fotos']['name']);


        /*
         * Percorre todas as fotos.
         */
        for ($i = 0; $i < $quantidadeFotos; $i++) {


            /*
             * Ignora arquivos com erro.
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
             * Verifica o tipo real da imagem.
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


            /*
             * Define a extensão.
             */
            $extensao =
                $tiposPermitidos[$tipoArquivo];


            /*
             * Cria um nome único.
             */
            $nomeArquivo =
                uniqid(
                    'local_',
                    true
                ) .
                '.' .
                $extensao;


            /*
             * Caminho físico do arquivo.
             */
            $caminhoArquivo =
                $diretorio .
                $nomeArquivo;


            /*
             * Salva a imagem na pasta.
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
             * Caminho salvo no banco.
             */
            $caminhoBanco =
                'uploads/fotos/' .
                $nomeArquivo;


            /*
             * Cadastra a foto no banco.
             */
            $resultado =
                $fotoController->cadastrar([
                    'id_local' => $idLocal,
                    'caminho' => $caminhoBanco,
                    'descricao' => 'Imagem do local'
                ]);


            /*
             * Se der erro no banco,
             * remove o arquivo físico.
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
     * Depois de cadastrar o local
     * e todas as imagens, volta para a lista.
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

    <script>
const inputFotos = document.getElementById('fotos');
const previewFotos = document.getElementById('preview-fotos');

if (inputFotos && previewFotos) {

    inputFotos.addEventListener('change', function () {

        previewFotos.innerHTML = '';

        const arquivos = Array.from(this.files);

        arquivos.forEach(function (arquivo) {

            if (!arquivo.type.startsWith('image/')) {
                return;
            }

            const leitor = new FileReader();

            leitor.onload = function (evento) {

                const item = document.createElement('div');
                item.className = 'preview-foto-item';

                item.innerHTML = `
                    <img src="${evento.target.result}" alt="Prévia da imagem">
                    <span>${arquivo.name}</span>
                `;

                previewFotos.appendChild(item);
            };

            leitor.readAsDataURL(arquivo);
        });
    });
}
</script>
</head>


<body>


<div class="admin-page">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="admin-sidebar">


        <div class="admin-brand">

            <h1>
                Tour Local
            </h1>

            <span>
                Painel administrativo
            </span>

        </div>


        <nav class="admin-menu">


            <div class="admin-menu-title">
                Menu
            </div>


            <a href="../index.php">

                Dashboard

            </a>


            <a
                href="index.php"
                class="active"
            >

                Locais

            </a>


            <a href="../categoria/index.php">

                Categorias

            </a>


            <a href="../cidade/index.php">

                Cidades

            </a>


            <a href="../usuario/index.php">

                Usuários

            </a>


            <a href="../perfil.php">

                Meu perfil

            </a>


        </nav>


        <div class="admin-sidebar-footer">

            <a href="../../logout.php">

                Sair

            </a>

        </div>


    </aside>



    <!-- =====================================================
         CONTEÚDO
    ====================================================== -->

    <div class="admin-content">


        <!-- HEADER -->

        <header class="admin-header">


            <div>

                <h2>
                    Cadastrar Local
                </h2>


                <p>
                    Adicione um novo local ao Tour Local.
                </p>

            </div>


        </header>



        <!-- CONTEÚDO PRINCIPAL -->

        <main class="admin-main">


            <section class="admin-section">


                <div class="admin-form">


                    <div class="admin-form-card">


                        <form
                            method="POST"
                            enctype="multipart/form-data"
                        >


                            <!-- NOME -->

                            <div class="admin-form-group">


                                <label for="nome">
                                    Nome do local
                                </label>


                                <input
                                    type="text"
                                    id="nome"
                                    name="nome"
                                    required
                                >


                            </div>



                            <!-- CIDADE -->

                            <div class="admin-form-group">


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

                            <div class="admin-form-group">


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

                            <div class="admin-form-group">


                                <label for="descricao">
                                    Descrição
                                </label>


                                <textarea
                                    id="descricao"
                                    name="descricao"
                                ></textarea>


                            </div>



                            <!-- FOTOS -->

                            <div class="admin-form-group">


                                <label for="fotos">
                                    Imagens do local
                                </label>


                                <div class="admin-photo-upload">


                                    <input
                                        type="file"
                                        id="fotos"
                                        name="fotos[]"
                                        accept="image/jpeg,image/png,image/webp"
                                        multiple
                                    >


                                    <p>

                                        Você pode selecionar
                                        várias imagens.

                                        <br>

                                        JPG, PNG ou WEBP.
                                        Máximo de 5 MB por imagem.

                                    </p>


                                </div>


                            </div>



                            <!-- ENDEREÇO -->

                            <div class="admin-form-group">


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

                            <div class="admin-form-group">


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

                            <div class="admin-form-group">


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

                            <div class="admin-form-group">


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

                            <div class="admin-form-group">


                                <label for="preco">
                                    Preço
                                </label>


                                <input
                                    type="number"
                                    id="preco"
                                    name="preco"
                                    step="0.01"
                                    min="0"
                                    placeholder="Opcional"
                                >


                                <small class="admin-form-help">

                                    Campo opcional. Pode ser usado
                                    para locais que possuem cobrança.

                                </small>


                            </div>



                            <!-- BOTÕES -->

                            <div class="admin-form-actions">


                                <button
                                    type="submit"
                                    name="cadastrar_local"
                                    class="btn btn-primary"
                                >

                                    Cadastrar local

                                </button>


                                <a
                                    href="index.php"
                                    class="btn"
                                >

                                    Cancelar

                                </a>


                            </div>


                        </form>


                    </div>


                </div>


            </section>


        </main>


    </div>


</div>


</body>

</html>
