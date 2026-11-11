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

$statusFiltro = isset($_GET['status']) ? strtolower(trim((string) $_GET['status'])) : 'todos';
$statusesPermitidos = ['pendente', 'ativa', 'suspensa', 'recusada'];

if ($statusFiltro !== 'todos' && !in_array($statusFiltro, $statusesPermitidos, true)) {
    $statusFiltro = 'todos';
}

if ($statusFiltro === 'todos') {
    $empresas = $empresaController->listar();
} else {
    $empresas = $empresaController->listar($statusFiltro);
}

$resumo = $empresaController->obterResumoPorStatus();

$mensagem = $_SESSION['empresa_admin_mensagem'] ?? '';
$erro = $_SESSION['empresa_admin_erro'] ?? '';
unset($_SESSION['empresa_admin_mensagem'], $_SESSION['empresa_admin_erro']);

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Empresas - ArTour</title>

    <link rel="stylesheet" href="/Turismo-Lafa/assets/css/global.css?v=2">
    <link rel="stylesheet" href="/Turismo-Lafa/assets/css/admin.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        .admin-main {
            padding: 28px 32px 40px;
        }

        .empresa-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .empresa-breadcrumb {
            color: #7a8d96;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .empresa-breadcrumb a {
            color: #0d5a70;
            text-decoration: none;
        }

        .empresa-title {
            font-size: 2rem;
            color: #183f4d;
            margin: 0;
        }

        .empresa-subtitle {
            margin-top: 6px;
            color: #5d7380;
        }

        .empresa-resumo {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .empresa-card {
            background: #ffffff;
            border: 1px solid #e6edf1;
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: 0 10px 20px rgba(13, 38, 47, 0.04);
        }

        .empresa-card-label {
            display: block;
            color: #7a8d96;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .empresa-card-valor {
            color: #183f4d;
            font-size: 1.7rem;
            font-weight: 700;
        }

        .empresa-filtros {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 18px;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid #e6edf1;
            border-radius: 12px;
        }

        .empresa-filtros form {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .empresa-filtros select {
            min-width: 180px;
            padding: 10px 12px;
            border: 1px solid #dfe8ee;
            border-radius: 8px;
            background: #fff;
            color: #183f4d;
        }

        .empresa-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            background: #0d5a70;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .empresa-table-wrap {
            background: #ffffff;
            border: 1px solid #e6edf1;
            border-radius: 12px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #edf2f4;
            vertical-align: top;
        }

        th {
            background: #f4f8fa;
            color: #375765;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        td {
            color: #24424b;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pendente { background: #fff3d6; color: #8a6700; }
        .status-ativa { background: #dff6ed; color: #146c46; }
        .status-suspensa { background: #ffe4d9; color: #9c4f2b; }
        .status-recusada { background: #fde2e2; color: #9d3030; }

        .status-sem-status { background: #edf2f4; color: #5d7380; }

        .empresa-acao {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 8px;
            color: #0d5a70;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid #dfeaf0;
            background: #f8fbfc;
        }

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
            .empresa-table-wrap {
                overflow-x: auto;
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
                Empresas
            </h2>

            <div class="admin-user">
                <div class="admin-user-icon">
                    <?= strtoupper(substr($_SESSION['nome_usuario'], 0, 1)) ?>
                </div>
                <span><?= htmlspecialchars($_SESSION['nome_usuario']) ?></span>
            </div>
        </header>

        <section class="admin-main">

            <div class="empresa-header">
                <div>
                    <div class="empresa-breadcrumb">
                        <a href="../index.php">Painel</a>
                        <span>›</span>
                        <span>Empresas</span>
                    </div>
                    <h1 class="empresa-title">Gestão de empresas</h1>
                    <p class="empresa-subtitle">Gerencie o cadastro, aprovação e status das empresas do ArTour.</p>
                </div>
            </div>

            <?php if ($mensagem !== ''): ?>
                <div class="empresa-alert success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <?php if ($erro !== ''): ?>
                <div class="empresa-alert error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="empresa-resumo">
                <div class="empresa-card">
                    <span class="empresa-card-label">Pendentes</span>
                    <div class="empresa-card-valor"><?= (int) ($resumo['pendente'] ?? 0) ?></div>
                </div>
                <div class="empresa-card">
                    <span class="empresa-card-label">Ativas</span>
                    <div class="empresa-card-valor"><?= (int) ($resumo['ativa'] ?? 0) ?></div>
                </div>
                <div class="empresa-card">
                    <span class="empresa-card-label">Suspensas</span>
                    <div class="empresa-card-valor"><?= (int) ($resumo['suspensa'] ?? 0) ?></div>
                </div>
                <div class="empresa-card">
                    <span class="empresa-card-label">Recusadas</span>
                    <div class="empresa-card-valor"><?= (int) ($resumo['recusada'] ?? 0) ?></div>
                </div>
            </div>

            <div class="empresa-filtros">
                <form method="GET">
                    <label for="status" style="font-weight:600; color:#375765;">Filtrar por status:</label>
                    <select id="status" name="status" onchange="this.form.submit()">
                        <option value="todos" <?= $statusFiltro === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="pendente" <?= $statusFiltro === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="ativa" <?= $statusFiltro === 'ativa' ? 'selected' : '' ?>>Ativa</option>
                        <option value="suspensa" <?= $statusFiltro === 'suspensa' ? 'selected' : '' ?>>Suspensa</option>
                        <option value="recusada" <?= $statusFiltro === 'recusada' ? 'selected' : '' ?>>Recusada</option>
                    </select>
                </form>
            </div>

            <div class="empresa-table-wrap">
                <?php if (empty($empresas)): ?>
                    <div style="padding: 24px; color: #5d7380;">
                        Nenhuma empresa encontrada para o filtro selecionado.
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>CNPJ</th>
                                <th>Responsável</th>
                                <th>Status</th>
                                <th>Contato</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($empresas as $empresa): ?>
                                <?php
                                    $status = strtolower((string) ($empresa['status'] ?? ''));
                                    $statusClasse = 'sem-status';

                                    if ($status === 'pendente') {
                                        $statusClasse = 'pendente';
                                    } elseif ($status === 'ativa') {
                                        $statusClasse = 'ativa';
                                    } elseif ($status === 'suspensa') {
                                        $statusClasse = 'suspensa';
                                    } elseif ($status === 'recusada') {
                                        $statusClasse = 'recusada';
                                    }

                                    $statusTexto = $status !== '' ? $status : 'Sem status';
                                    $nomeFantasia = $empresa['nome_fantasia'] ?? 'Sem nome';
                                    $cnpj = $empresa['cnpj'] ?? '';
                                    $cnpjFormatado = $cnpj !== '' ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj) : 'Não informado';
                                    $responsavel = $empresa['responsavel_nome'] ?? 'Não informado';
                                    $email = $empresa['responsavel_email'] ?? ($empresa['email_contato'] ?? 'Não informado');
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($nomeFantasia) ?></strong><br>
                                        <small><?= htmlspecialchars($empresa['razao_social'] ?? 'Sem razão social') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($cnpjFormatado) ?></td>
                                    <td><?= htmlspecialchars($responsavel) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= htmlspecialchars($statusClasse) ?>"><?= htmlspecialchars($statusTexto) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($email) ?></td>
                                    <td>
                                        <a href="detalhes.php?id=<?= (int) ($empresa['id_empresa'] ?? 0) ?>" class="empresa-acao">
                                            <i class="fa-solid fa-eye"></i>
                                            Detalhes
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </section>

    </main>

</div>

</body>

</html>
