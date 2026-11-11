<?php

require_once '../controller/EmpresaController.php';

$mensagem = '';
$tipoMensagem = 'info';

if (isset($_POST['cadastrar_empresa'])) {
    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? '',
        'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
        'nome_fantasia' => $_POST['nome_fantasia'] ?? '',
        'razao_social' => $_POST['razao_social'] ?? '',
        'documento' => $_POST['documento'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'email_contato' => $_POST['email_contato'] ?? '',
    ];

    $empresaController = new EmpresaController();

    try {
        $empresaController->cadastrar($dados);
        $mensagem = 'Cadastro solicitado com sucesso. Sua empresa foi registrada com status pendente.';
        $tipoMensagem = 'sucesso';
    } catch (Throwable $e) {
        $mensagem = $e->getMessage();
        $tipoMensagem = 'erro';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Empresa - ArTour</title>
    <link rel="icon" type="image/png" href="/Turismo-Lafa/assets/img/artour-simbolo.png">
    <link rel="stylesheet" href="/Turismo-Lafa/assets/css/site.css?v=2">

    <style>
        body {
            background: linear-gradient(135deg, #edf5f6 0%, #f8f5ee 100%);
            color: #123b4a;
        }

        .cadastro-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
        }

        .cadastro-card {
            width: min(100%, 640px);
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #dfeae7;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(7, 63, 77, 0.12);
            padding: 2.25rem 2rem;
        }

        .cadastro-brand {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .cadastro-brand h1 {
            margin: 0;
            color: #073F4D;
            font: 800 2.2rem/1.1 'Poppins', sans-serif;
            letter-spacing: -0.06em;
        }

        .cadastro-brand h1::after {
            content: ".";
            color: #39C6C3;
        }

        .cadastro-brand p {
            margin: 0.4rem 0 0;
            color: #5c7278;
            font-size: 0.96rem;
        }

        .cadastro-form {
            display: grid;
            gap: 1rem;
        }

        .cadastro-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .cadastro-grupo {
            display: grid;
            gap: 0.45rem;
        }

        .cadastro-form label {
            font-weight: 600;
            color: #1f4451;
        }

        .cadastro-form input,
        .cadastro-form textarea {
            width: 100%;
            min-height: 48px;
            padding: 0.8rem 0.9rem;
            border: 1px solid #cfe0e3;
            border-radius: 12px;
            background: #fff;
            font: inherit;
            color: #123b4a;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .cadastro-form textarea {
            min-height: 110px;
            resize: vertical;
        }

        .cadastro-form input:focus,
        .cadastro-form textarea:focus {
            outline: none;
            border-color: #39C6C3;
            box-shadow: 0 0 0 4px rgba(57, 198, 195, 0.15);
        }

        .cadastro-button {
            margin-top: 0.5rem;
            border: 0;
            border-radius: 12px;
            background: #073F4D;
            color: #fff;
            min-height: 50px;
            font: 700 1rem 'Inter', sans-serif;
            cursor: pointer;
        }

        .cadastro-button:hover {
            background: #0b4a5d;
        }

        .cadastro-mensagem {
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .cadastro-mensagem.sucesso {
            background: #edf9f2;
            color: #176742;
            border: 1px solid #caebd7;
        }

        .cadastro-mensagem.erro {
            background: #fff1f2;
            color: #a1283d;
            border: 1px solid #f7c8d0;
        }

        .cadastro-rodape {
            margin-top: 1.35rem;
            text-align: center;
            color: #5d7278;
            font-size: 0.97rem;
        }

        .cadastro-rodape a {
            color: #073F4D;
            font-weight: 700;
        }

        @media (max-width: 640px) {
            .cadastro-grid {
                grid-template-columns: 1fr;
            }

            .cadastro-card {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>

<body>

    <main class="cadastro-page">

        <section class="cadastro-card">

            <div class="cadastro-brand">
                <h1>ArTour</h1>
                <p>Cadastre sua empresa e comece a divulgar experiências.</p>
            </div>

            <?php if ($mensagem !== ''): ?>
                <div class="cadastro-mensagem <?= htmlspecialchars($tipoMensagem) ?>">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="cadastro-form">
                <div class="cadastro-grid">
                    <div class="cadastro-grupo">
                        <label for="nome">Nome do responsável</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>

                    <div class="cadastro-grupo">
                        <label for="email">E-mail da conta</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="cadastro-grid">
                    <div class="cadastro-grupo">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>

                    <div class="cadastro-grupo">
                        <label for="confirmar_senha">Confirmar senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                    </div>
                </div>

                <div class="cadastro-grid">
                    <div class="cadastro-grupo">
                        <label for="nome_fantasia">Nome fantasia</label>
                        <input type="text" id="nome_fantasia" name="nome_fantasia" required>
                    </div>

                    <div class="cadastro-grupo">
                        <label for="documento">CNPJ</label>
                        <input type="text" id="documento" name="documento" maxlength="18" placeholder="00.000.000/0000-00" required>
                    </div>
                </div>

                <div class="cadastro-grupo">
                    <label for="razao_social">Razão social</label>
                    <input type="text" id="razao_social" name="razao_social" required>
                </div>

                <div class="cadastro-grupo">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descreva sua empresa"></textarea>
                </div>

                <div class="cadastro-grid">
                    <div class="cadastro-grupo">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" placeholder="(31) 99999-9999">
                    </div>

                    <div class="cadastro-grupo">
                        <label for="email_contato">E-mail de contato</label>
                        <input type="email" id="email_contato" name="email_contato">
                    </div>
                </div>

                <button type="submit" name="cadastrar_empresa" class="cadastro-button">
                    Cadastrar empresa
                </button>
            </form>

            <div class="cadastro-rodape">
                <p>Já tem conta? <a href="login.php">Entrar</a></p>
            </div>

        </section>

    </main>

</body>

</html>
