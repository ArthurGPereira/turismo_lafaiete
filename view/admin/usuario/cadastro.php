
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

require_once __DIR__ . '/../../../controller/UsuarioController.php';

$usuarioController = new UsuarioController();
$mensagem = '';

$nome = '';
$email = '';
$tipo = 'usuario';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'usuario';

    if ($nome === '' || $email === '' || $senha === '') {

        $mensagem = 'Preencha todos os campos obrigatórios.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Informe um e-mail válido.';

    } elseif (!in_array($tipo, ['usuario', 'administrador'], true)) {

        $mensagem = 'Tipo de usuário inválido.';

    } else {

        $dados = [
            'nome' => $nome,
            'email' => $email,
            'senha' => $senha,
            'tipo' => $tipo,
            'imagem' => null
        ];

        try {
            if ($usuarioController->cadastrar($dados)) {
                header('Location: index.php');
                exit;
            }

            $mensagem = 'Não foi possível cadastrar o usuário.';
        } catch (PDOException $e) {
            $mensagem = $e->getCode() === '23000'
                ? 'Este e-mail já está cadastrado.'
                : 'Erro ao cadastrar. Confira os dados e tente novamente.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Novo usuário - ArTour</title>

    <link rel="icon" type="image/png"
          href="../../../assets/img/artour-simbolo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
          href="../../../assets/css/global.css">

    <link rel="stylesheet"
          href="../../../assets/css/admin.css">

    <link rel="stylesheet"
          href="../../../assets/css/usuario.css?v=4">
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

            <a href="../cidade/index.php">
                <i class="fa-solid fa-city"></i>
                <span>Cidades</span>
            </a>

            <a href="index.php" class="active">
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
                <i class="fa-solid fa-user-plus"></i>
                Novo usuário
            </h2>

            <div class="admin-user-info">
                <span class="admin-user-avatar">A</span>
                <span>Administrador</span>
            </div>
        </header>

        <section class="admin-main">

            <div class="admin-section usuario-cadastro-section">

                <div class="admin-section-header">
                    <div>
                        <span class="usuario-cadastro-etiqueta">GERENCIAMENTO DE ACESSO</span>
                        <h3>Cadastrar usuário</h3>
                        <p>Adicione uma nova conta ao sistema ArTour.</p>
                    </div>
                </div>

                <?php if ($mensagem !== ''): ?>
                    <div class="admin-alert admin-alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="usuario-cadastro-form">

                    <div class="admin-form-group">
                        <label for="nome">Nome completo</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Digite o nome completo"
                            maxlength="150"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <div class="admin-form-group">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="exemplo@email.com"
                            maxlength="150"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <div class="admin-form-group">
                        <label for="senha">Senha</label>
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Crie uma senha"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="admin-form-group">
                        <label for="tipo">Tipo de usuário</label>
                        <select id="tipo" name="tipo" required>
                            <option value="usuario"
                                <?= $tipo === 'usuario' ? 'selected' : '' ?>>
                                Usuário
                            </option>

                            <option value="administrador"
                                <?= $tipo === 'administrador' ? 'selected' : '' ?>>
                                Administrador
                            </option>
                        </select>
                    </div>

                    <div class="admin-form-actions">
                        <a href="index.php" class="btn btn-secondary">
                            Cancelar
                        </a>

                        <button type="submit" name="cadastrar" class="btn">
                            <i class="fa-solid fa-user-plus"></i>
                            Cadastrar usuário
                        </button>
                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>
</html>
