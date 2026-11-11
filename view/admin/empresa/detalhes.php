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

if (empty($_SESSION['csrf_token_empresa'])) {
    $_SESSION['csrf_token_empresa'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../../../controller/EmpresaController.php';

$empresaController = new EmpresaController();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$idEmpresa = (int) $_GET['id'];
$empresa = $empresaController->buscarPorId($idEmpresa);

if (!$empresa) {
    $_SESSION['empresa_admin_erro'] = 'Empresa não encontrada.';
    header('Location: index.php');
    exit;
}

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['acao'], $_POST['empresa_id'], $_POST['status_destino'], $_POST['csrf'])) {
        $erro = 'Dados inválidos para alteração de status.';
    } elseif (!hash_equals($_SESSION['csrf_token_empresa'], (string) $_POST['csrf'])) {
        $erro = 'Sessão inválida. Tente novamente.';
    } else {
        $empresaId = (int) $_POST['empresa_id'];
        $statusDestino = strtolower(trim((string) $_POST['status_destino']));

        try {
            $empresaController->alterarStatus($empresaId, $statusDestino);
            $_SESSION['empresa_admin_mensagem'] = 'Status atualizado com sucesso.';
            header('Location: detalhes.php?id=' . $empresaId);
            exit;
        } catch (Throwable $e) {
            $erro = $e->getMessage();
        }
    }
}

$statusAtual = strtolower(trim((string) ($empresa['status'] ?? '')));
$exibirStatus = $statusAtual !== '' ? $statusAtual : 'sem-status';
$cnpj = $empresa['cnpj'] ?? '';
$cnpjFormatado = $cnpj !== '' ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj) : 'Não informado';
$nomeFantasia = $empresa['nome_fantasia'] ?? 'Sem nome';
$razaoSocial = $empresa['razao_social'] ?? 'Não informado';
$descricao = $empresa['descricao'] ?? 'Sem descrição.';
$telefone = $empresa['telefone'] ?? 'Não informado';
$emailContato = $empresa['email_contato'] ?? 'Não informado';
$responsavel = $empresa['responsavel_nome'] ?? 'Não informado';
$responsavelEmail = $empresa['responsavel_email'] ?? 'Não informado';

$statusLabel = $statusAtual !== '' ? $statusAtual : 'Sem status';
$statusClasse = $statusAtual !== '' ? $statusAtual : 'sem-status';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalhes da Empresa - ArTour</title>

    <link rel="stylesheet" href="/Turismo-Lafa/assets/css/global.css?v=2">
    <link rel="stylesheet" href="/Turismo-Lafa/assets/css/admin.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        .admin-main {
            padding: 28px 32px 40px;
        }

        .empresa-breadcrumb {
            margin-bottom: 8px;
            color: #7a8d96;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .empresa-breadcrumb a {
            color: #0d5a70;
            text-decoration: none;
        }

        .empresa-box {
            background: #ffffff;
            border: 1px solid #e6edf1;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 10px 22px rgba(15, 45, 56, 0.04);
        }

        .empresa-layout {
            display: grid;
            grid-template-columns: 1.6fr 0.9fr;
            gap: 22px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
            gap: 16px;
        }

        .info-item {
            background: #f8fbfc;
            border: 1px solid #edf2f4;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .info-item label {
            display: block;
            margin-bottom: 8px;
            color: #718894;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .info-item strong {
            color: #183f4d;
            font-size: 15px;
            line-height: 1.5;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 11px;
        }

        .status-pendente { background: #fff3d6; color: #8a6700; }
        .status-ativa { background: #dff6ed; color: #146c46; }
        .status-suspensa { background: #ffe4d9; color: #9c4f2b; }
        .status-recusada { background: #fde2e2; color: #9d3030; }
        .sem-status { background: #f0f0f0; color: #a0a0a0; }

        .empresa-actions {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .empresa-action-form {
            background: #f8fbfc;
            border: 1px solid #edf2f4;
            border-radius: 10px;
            padding: 14px;
        }

        .empresa-action-form button {
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 10px 12px;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
            background: #0d5a70;
        }

        .empresa-action-form button.acao-recusar { background: #b84545; }
        .empresa-action-form button.acao-suspender { background: #d47a33; }
        .empresa-action-form button.acao-reativar { background: #218a5c; }

        .empresa-alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            font-weight: 600;
        }

        .empresa-alert.success {
            background: #eafaf1;
            color: #12643a;
            border: 1px solid #c7ecd7;
        }

        .empresa-alert.error {
            background: #fff0f0;
            color: #8e2d2d;
            border: 1px solid #f1c8c8;
        }

        @media (max-width: 900px) {
            .empresa-layout,
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                <i class="fa-solid fa-building"></i>
                <span>Empresas</span>
            </a>

            <a href="../usuario/index.php">
                <i class="fa-solid fa-users"></i>
                <span>Usuários</span>
            </a>

            <div class="admin-menu-title">Sistema</div>

            <a href="/Turismo-Lafa/index.php">
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
                <i class="fa-solid fa-building"></i>
                Detalhes da empresa
            </h2>

            <div class="admin-user">
                <div class="admin-user-icon">
                    <?= strtoupper(substr($_SESSION['nome_usuario'], 0, 1)) ?>
                </div>
                <span><?= htmlspecialchars($_SESSION['nome_usuario']) ?></span>
            </div>
        </header>

        <section class="admin-main">

            <div class="empresa-breadcrumb">
                <a href="index.php">Empresas</a>
                <span>›</span>
                <span><?= htmlspecialchars($nomeFantasia) ?></span>
            </div>

            <div style="margin: 0 0 18px;">
                <a href="index.php" class="empresa-btn" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none;">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar para empresas</span>
                </a>
            </div>

            <?php if ($mensagem !== ''): ?>
                <div class="empresa-alert success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <?php if ($erro !== ''): ?>
                <div class="empresa-alert error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="empresa-box">
                <div class="empresa-layout">
                    <div>
                        <h2 style="margin:0 0 18px; color:#183f4d;"><?= htmlspecialchars($nomeFantasia) ?></h2>

                        <div class="info-grid">
                            <div class="info-item">
                                <label>Nome fantasia</label>
                                <strong><?= htmlspecialchars($nomeFantasia) ?></strong>
                            </div>
                            <div class="info-item">
                                <label>Razão social</label>
                                <strong><?= htmlspecialchars($razaoSocial) ?></strong>
                            </div>
                            <div class="info-item">
                                <label>CNPJ</label>
                                <strong><?= htmlspecialchars($cnpjFormatado) ?></strong>
                            </div>
                            <div class="info-item">
                                <label>Status</label>
                                <span class="status-badge status-<?= htmlspecialchars($statusClasse) ?>"><?= htmlspecialchars($statusLabel) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Telefone</label>
                                <strong><?= htmlspecialchars($telefone) ?></strong>
                            </div>
                            <div class="info-item">
                                <label>E-mail de contato</label>
                                <strong><?= htmlspecialchars($emailContato) ?></strong>
                            </div>
                            <div class="info-item" style="grid-column:1 / -1;">
                                <label>Descrição</label>
                                <strong><?= htmlspecialchars($descricao) ?></strong>
                            </div>
                            <div class="info-item" style="grid-column:1 / -1;">
                                <label>Responsável</label>
                                <strong><?= htmlspecialchars($responsavel) ?> · <?= htmlspecialchars($responsavelEmail) ?></strong>
                            </div>
                        </div>
                    </div>

                    <aside>
                        <h3 style="margin:0 0 16px; color:#183f4d;">Ações de status</h3>

                        <div class="empresa-actions">
                            <?php if ($statusAtual === 'pendente'): ?>
                                <form method="POST" class="empresa-action-form">
                                    <input type="hidden" name="empresa_id" value="<?= (int) $empresa['id_empresa'] ?>">
                                    <input type="hidden" name="status_destino" value="ativa">
                                    <input type="hidden" name="acao" value="aprovar">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token_empresa']) ?>">
                                    <button type="submit">Aprovar empresa</button>
                                </form>

                                <form method="POST" class="empresa-action-form">
                                    <input type="hidden" name="empresa_id" value="<?= (int) $empresa['id_empresa'] ?>">
                                    <input type="hidden" name="status_destino" value="recusada">
                                    <input type="hidden" name="acao" value="recusar">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token_empresa']) ?>">
                                    <button type="submit" class="acao-recusar">Recusar empresa</button>
                                </form>
                            <?php elseif ($statusAtual === ''): ?>
                                <div class="empresa-alert error">Esta empresa não possui status válido para aprovação.</div>
                            <?php endif; ?>

                            <?php if ($statusAtual === 'ativa'): ?>
                                <form method="POST" class="empresa-action-form">
                                    <input type="hidden" name="empresa_id" value="<?= (int) $empresa['id_empresa'] ?>">
                                    <input type="hidden" name="status_destino" value="suspensa">
                                    <input type="hidden" name="acao" value="suspender">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token_empresa']) ?>">
                                    <button type="submit" class="acao-suspender">Suspender empresa</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($statusAtual === 'suspensa' || $statusAtual === 'recusada'): ?>
                                <form method="POST" class="empresa-action-form">
                                    <input type="hidden" name="empresa_id" value="<?= (int) $empresa['id_empresa'] ?>">
                                    <input type="hidden" name="status_destino" value="ativa">
                                    <input type="hidden" name="acao" value="reativar">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token_empresa']) ?>">
                                    <button type="submit" class="acao-reativar">Reativar empresa</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </aside>
                </div>
            </div>

        </section>

    </main>

</div>

</body>

</html>
