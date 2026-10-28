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

require_once '../../../controller/CidadeController.php';

$cidadeController = new CidadeController();

$mensagem = '';

if (isset($_POST['cadastrar'])) {

    $nome = trim($_POST['nome']);
    $estado = trim($_POST['estado']);
    $descricao = trim($_POST['descricao']);
    $historia = trim($_POST['historia']);

    $imagem = null;

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

    $arquivo = $_FILES['imagem'];

    $extensao = strtolower(
        pathinfo($arquivo['name'], PATHINFO_EXTENSION)
    );

    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extensao, $extensoesPermitidas)) {

        $mensagem = 'Formato de imagem não permitido.';

    } elseif ($arquivo['size'] > 5 * 1024 * 1024) {

        $mensagem = 'A imagem deve ter no máximo 5 MB.';

    } else {

        $nomeArquivo = uniqid('cidade_') . '.' . $extensao;

        $pasta = '../../../uploads/cidades/';

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $caminho = $pasta . $nomeArquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {

            $imagem = 'uploads/cidades/' . $nomeArquivo;

        } else {

            $mensagem = 'Não foi possível salvar a imagem.';
        }
    }
}

    if ($nome === '' || $estado === '') {

        $mensagem = 'Preencha o nome da cidade e o estado.';

    } else {

        $dados = [
    'nome' => $nome,
    'estado' => $estado,
    'descricao' => $descricao,
    'historia' => $historia,
    'imagem' => $imagem,
    'status' => 'ativo'
    ];

        if ($cidadeController->cadastrar($dados)) {

            header('Location: index.php');
            exit;

        } else {

            $mensagem = 'Não foi possível cadastrar a cidade.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova cidade - ArTour</title>

    <link rel="icon" type="image/png"
          href="../../../assets/img/artour-simbolo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="../../../assets/css/global.css">
    <link rel="stylesheet" href="../../../assets/css/admin.css">
    <link rel="stylesheet" href="../../../assets/css/cidade.css?v=11">
    <link rel="stylesheet" href="../../../assets/css/cidade-cadastro.css?v=1">
</head>

<body>

    <header>

        <h1>ArTour</h1>

        <p>Painel administrativo</p>

    </header>


    <main class="cidade-cadastro">

        <div class="cidade-cadastro-header">

            <div class="cidade-breadcrumb">

                <a href="index.php">
                    Cidades
                </a>

                <span>›</span>

                <span>Nova cidade</span>

            </div>


        <h2>
            Adicionar novo destino
        </h2>

        <p>
            Cadastre uma nova cidade para começar a apresentar seus lugares,
            atrações e experiências no ArTour.
        </p>

        </div>


        <?php if ($mensagem !== ''): ?>

            <div class="cidade-alert">

                <i class="fa-solid fa-circle-exclamation"></i>

                <span>
                    <?= htmlspecialchars($mensagem) ?>
                </span>

            </div>

        <?php endif; ?>


        <form method="POST" enctype="multipart/form-data">

            <div class="cidade-form-card">


                <!-- INFORMAÇÕES BÁSICAS -->

                <section class="cidade-form-section">

                    <div class="cidade-section-title">

                        <i class="fa-solid fa-location-dot"></i>

                        <h3>
                            Informações básicas
                        </h3>

                    </div>


                    <div class="cidade-form-grid">

                        <div class="cidade-form-group">

                            <label for="nome">
                                Nome da cidade
                            </label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                placeholder="Ex.: Ouro Preto"
                                required
                            >

                        </div>


                        <div class="cidade-form-group">

    <label for="estado">
        Estado
    </label>

    <select
        id="estado"
        name="estado"
        required
    >

        <option value="" selected disabled>
            Selecione o estado
        </option>

        <option value="Acre">Acre</option>
        <option value="Alagoas">Alagoas</option>
        <option value="Amapá">Amapá</option>
        <option value="Amazonas">Amazonas</option>
        <option value="Bahia">Bahia</option>
        <option value="Ceará">Ceará</option>
        <option value="Distrito Federal">Distrito Federal</option>
        <option value="Espírito Santo">Espírito Santo</option>
        <option value="Goiás">Goiás</option>
        <option value="Maranhão">Maranhão</option>
        <option value="Mato Grosso">Mato Grosso</option>
        <option value="Mato Grosso do Sul">Mato Grosso do Sul</option>
        <option value="Minas Gerais">Minas Gerais</option>
        <option value="Pará">Pará</option>
        <option value="Paraíba">Paraíba</option>
        <option value="Paraná">Paraná</option>
        <option value="Pernambuco">Pernambuco</option>
        <option value="Piauí">Piauí</option>
        <option value="Rio de Janeiro">Rio de Janeiro</option>
        <option value="Rio Grande do Norte">Rio Grande do Norte</option>
        <option value="Rio Grande do Sul">Rio Grande do Sul</option>
        <option value="Rondônia">Rondônia</option>
        <option value="Roraima">Roraima</option>
        <option value="Santa Catarina">Santa Catarina</option>
        <option value="São Paulo">São Paulo</option>
        <option value="Sergipe">Sergipe</option>
        <option value="Tocantins">Tocantins</option>

    </select>

</div>

                    </div>

                </section>


                <!-- DESCRIÇÃO -->

                <section class="cidade-form-section">

                    <div class="cidade-section-title">

                        <i class="fa-solid fa-align-left"></i>

                        <h3>
                            Sobre a cidade
                        </h3>

                    </div>


                    <div class="cidade-form-grid">

                        <div class="cidade-form-group cidade-full">

                            <label for="descricao">
                                Descrição
                            </label>

                            <textarea
                                id="descricao"
                                name="descricao"
                                placeholder="Escreva uma breve apresentação da cidade..."
                            ></textarea>

                            <span class="cidade-form-help">
                                Essa informação poderá ser exibida para os visitantes.
                            </span>

                        </div>

                    </div>

                </section>
<!-- IMAGEM -->

<section class="cidade-form-section">

    <div class="cidade-section-title">

        <i class="fa-solid fa-image"></i>

        <h3>
            Imagem da cidade
        </h3>

    </div>

    <div class="cidade-form-grid">

        <div class="cidade-form-group cidade-full">

            <label for="imagem">
                Foto principal
            </label>

            <input
                type="file"
                id="imagem"
                name="imagem"
                accept="image/jpeg,image/png,image/webp"
            >

            <span class="cidade-form-help">
                JPG, PNG ou WEBP. Tamanho máximo de 5 MB.
            </span>

        </div>

    </div>

</section>

                <!-- HISTÓRIA -->

                <section class="cidade-form-section">

                    <div class="cidade-section-title">

                        <i class="fa-solid fa-book-open"></i>

                        <h3>
                            História
                        </h3>

                    </div>


                    <div class="cidade-form-grid">

                        <div class="cidade-form-group cidade-full">

                            <label for="historia">
                                História da cidade
                            </label>

                            <textarea
                                id="historia"
                                name="historia"
                                placeholder="Conte um pouco sobre a história e características da cidade..."
                            ></textarea>

                            <span class="cidade-form-help">
                                Você pode adicionar informações históricas importantes sobre o destino.
                            </span>

                        </div>

                    </div>

                </section>


                <!-- AÇÕES -->

                <div class="cidade-form-actions">

                    <a
                        href="index.php"
                        class="cidade-back"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Voltar para cidades

                    </a>


                    <div class="cidade-actions-right">

                        <a
                            href="index.php"
                            class="cidade-btn-cancelar"
                        >
                            Cancelar
                        </a>


                        <button
                            type="submit"
                            name="cadastrar"
                            class="cidade-btn-salvar"
                        >

                            <i class="fa-solid fa-plus"></i>

                            Cadastrar cidade

                        </button>

                    </div>

                </div>


            </div>

        </form>

    </main>

</body>

</html>
