
<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../login.php');
    exit;
}

if (($_SESSION['tipo_usuario'] ?? '') !== 'administrador') {
    header('Location: ../../../index.php');
    exit;
}

require_once __DIR__ . '/../../../controller/CidadeController.php';

$cidadeController = new CidadeController();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$cidade = $cidadeController->buscarPorId($id);

if (!$cidade) {
    header('Location: index.php');
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_cidade'])) {

    $nome = trim($_POST['nome'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $historia = trim($_POST['historia'] ?? '');

    if ($nome === '' || $estado === '') {
        $mensagem = 'Preencha o nome da cidade e o estado.';
    } else {
        $dados = [
            'id_cidade' => $id,
            'nome' => $nome,
            'estado' => $estado,
            'descricao' => $descricao,
            'historia' => $historia,
            'status' => $cidade['status'] ?? 'ativo'
        ];

        if ($cidadeController->editar($dados)) {
            header('Location: index.php');
            exit;
        }

        $mensagem = 'Não foi possível salvar as alterações.';
    }

    $cidade = array_merge($cidade, [
        'nome' => $nome,
        'estado' => $estado,
        'descricao' => $descricao,
        'historia' => $historia
    ]);
}

function e($valor)
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar cidade - ArTour</title>

    <link rel="icon" type="image/png"
          href="../../../assets/img/artour-simbolo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
          href="../../../assets/css/global.css">

    <link rel="stylesheet"
          href="../../../assets/css/admin.css">

    <link rel="stylesheet"
          href="../../../assets/css/cidade-editar.css?v=2">
</head>

<body>

<div class="admin-page">

    <aside class="admin-sidebar">

        <div class="admin-brand">
            <h1>ArTour</h1>
            <span>Assistente Regional de Turismo</span>
        </div>

        <nav class="admin-menu">

            <div class="admin-menu-title">Gerenciamento</div>

            <a href="../index.php">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a href="../local/index.php">
                <i class="fa-solid fa-location-dot"></i>
                <span>Locais</span>
            </a>

            <a href="../categoria/index.php">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categorias</span>
            </a>

            <a href="index.php" class="active">
                <i class="fa-solid fa-city"></i>
                <span>Cidades</span>
            </a>

            <a href="../usuario/index.php">
                <i class="fa-solid fa-users"></i>
                <span>Usuários</span>
            </a>

            <div class="admin-menu-title">Sistema</div>

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
                <i class="fa-solid fa-city"></i>
                Editar cidade
            </h2>
        </header>

        <section class="admin-main">

            <div class="cidade-editar">

                <div class="cidade-editar-breadcrumb">
                    <a href="../index.php">Dashboard</a>
                    <i class="fa-solid fa-chevron-right"></i>
                    <a href="index.php">Cidades</a>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span>Editar cidade</span>
                </div>

                <div class="cidade-editar-header">
                    <h2>Editar cidade</h2>
                    <p>Atualize as informações deste destino no ArTour.</p>
                </div>

                <div class="cidade-editar-card">

                    <div class="cidade-editar-card-header">
                        <h3>
                            <i class="fa-solid fa-pen-to-square"></i>
                            Informações da cidade
                        </h3>
                        <p>Altere os dados abaixo e salve as modificações.</p>
                    </div>

                    <?php if ($mensagem !== ''): ?>
                        <div class="cidade-editar-alerta">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <?= e($mensagem) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="cidade-editar-secao">

                            <div class="cidade-editar-secao-titulo">
                                <i class="fa-solid fa-location-dot"></i>
                                <h4>Informações básicas</h4>
                            </div>

                            <div class="cidade-editar-grid">

                                <div class="cidade-editar-campo">
                                    <label for="nome">Nome da cidade *</label>
                                    <input
                                        type="text"
                                        id="nome"
                                        name="nome"
                                        maxlength="100"
                                        value="<?= e($cidade['nome']) ?>"
                                        required
                                    >
                                </div>

                                <div class="cidade-editar-campo">
                                    <label for="estado">Estado *</label>
                                    <input
                                        type="text"
                                        id="estado"
                                        name="estado"
                                        maxlength="100"
                                        value="<?= e($cidade['estado']) ?>"
                                        required
                                    >
                                </div>

                            </div>
                        </div>

                        <div class="cidade-editar-secao">

                            <div class="cidade-editar-secao-titulo">
                                <i class="fa-solid fa-align-left"></i>
                                <h4>Sobre a cidade</h4>
                            </div>

                            <div class="cidade-editar-campo">
                                <label for="descricao">Descrição</label>
                                <textarea
                                    id="descricao"
                                    name="descricao"
                                    rows="5"
                                    placeholder="Escreva uma breve apresentação da cidade."
                                ><?= e($cidade['descricao']) ?></textarea>
                                <span class="cidade-editar-ajuda">
                                    Essa informação poderá ser exibida aos visitantes.
                                </span>
                            </div>

                        </div>

                        <div class="cidade-editar-secao">

                            <div class="cidade-editar-secao-titulo">
                                <i class="fa-solid fa-book-open"></i>
                                <h4>História</h4>
                            </div>

                            <div class="cidade-editar-campo">
                                <label for="historia">História da cidade</label>
                                <textarea
                                    id="historia"
                                    name="historia"
                                    rows="7"
                                    placeholder="Conte um pouco sobre a história e a cultura do destino."
                                ><?= e($cidade['historia']) ?></textarea>
                                <span class="cidade-editar-ajuda">
                                    Adicione informações históricas e culturais relevantes.
                                </span>
                            </div>

                        </div>

                        <div class="cidade-editar-acoes">

                            <a href="index.php"
                               class="cidade-btn-secundario">
                                <i class="fa-solid fa-arrow-left"></i>
                                Cancelar
                            </a>

                            <button type="submit" name="editar_cidade">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Salvar alterações
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>
