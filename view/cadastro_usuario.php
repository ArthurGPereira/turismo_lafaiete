<?php

require_once '../controller/UsuarioController.php';

$mensagem = '';

if (isset($_POST['cadastrar'])) {

    $dados = [
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'senha' => $_POST['senha'],
        'tipo' => 'usuario'
    ];

    $usuarioController = new UsuarioController();

    try {

        $usuarioController->cadastrar($dados);

        $mensagem = 'Usuário cadastrado com sucesso!';

    } catch (PDOException $e) {

        $mensagem = 'Erro ao cadastrar. O e-mail pode já estar cadastrado.';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Usuário - ArTour</title>

    <link rel="icon" type="image/png" href="/Turismo-Lafa/assets/img/artour-simbolo.png">
    <link rel="stylesheet" href="/Turismo-Lafa/assets/css/site.css?v=2">

    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes backgroundZoom {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(1.08);
            }
        }

        @keyframes messageFadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body {
            position: relative;
            background: linear-gradient(135deg, #edf5f6 0%, #f8f5ee 100%);
            color: #123b4a;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(237, 245, 246, 0.72), rgba(248, 245, 238, 0.7)),
                url('/Turismo-Lafa/assets/img/praca-tiradentes.jpg') center center / cover no-repeat;
            z-index: -2;
            animation: backgroundZoom 18s ease-in-out infinite alternate;
        }

        .cadastro-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
            background: rgba(7, 63, 77, 0.08);
        }

        .cadastro-card {
            width: min(100%, 500px);
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid #dfeae7;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(7, 63, 77, 0.12);
            padding: 2.25rem 2rem;
            animation: fadeUp 0.7s ease-out both;
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

        .cadastro-grupo {
            display: grid;
            gap: 0.45rem;
        }

        .cadastro-form label {
            font-weight: 600;
            color: #1f4451;
        }

        .cadastro-form input {
            width: 100%;
            min-height: 48px;
            padding: 0.8rem 0.9rem;
            border: 1px solid #cfe0e3;
            border-radius: 12px;
            background: #fff;
            font: inherit;
            color: #123b4a;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.2s ease, background-color 0.2s ease;
        }

        .cadastro-form input:hover {
            border-color: #9fc7ca;
            background: #fcfffe;
        }

        .cadastro-form input:focus {
            outline: none;
            border-color: #0d5c6a;
            box-shadow: 0 0 0 4px rgba(13, 92, 106, 0.12);
            transform: translateY(-1px);
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
            transition: transform 0.2s ease, opacity 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 8px 18px rgba(7, 63, 77, 0.12);
        }

        .cadastro-button:hover {
            background: #0b4a5d;
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(7, 63, 77, 0.18);
        }

        .cadastro-button:focus-visible {
            outline: 3px solid rgba(57, 198, 195, 0.35);
            outline-offset: 2px;
        }

        .cadastro-mensagem {
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            background: #edf9f2;
            color: #176742;
            border: 1px solid #caebd7;
            animation: messageFadeIn 0.45s ease-out;
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

        @media (max-width: 480px) {
            .cadastro-card {
                padding: 1.5rem 1rem;
            }

            .cadastro-brand h1 {
                font-size: 1.9rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

</head>

<body>

    <main class="cadastro-page">

        <section class="cadastro-card">

            <div class="cadastro-brand">
                <h1>ArTour</h1>
                <p>Crie sua conta para explorar destinos e experiências.</p>
            </div>

            <?php if ($mensagem != ''): ?>

                <div class="cadastro-mensagem">
                    <?= htmlspecialchars($mensagem) ?>
                </div>

            <?php endif; ?>

            <form method="POST" class="cadastro-form">

                <div class="cadastro-grupo">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required>
                </div>

                <div class="cadastro-grupo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="cadastro-grupo">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>

                <button type="submit" name="cadastrar" class="cadastro-button">
                    Cadastrar
                </button>

            </form>

            <div class="cadastro-rodape">
                <p>Já tem conta? <a href="login.php">Entrar</a></p>
            </div>

        </section>

    </main>

</body>

</html>
