<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once '../controller/UsuarioController.php';
require_once '../controller/FavoritoController.php';

$usuarioController = new UsuarioController();
$favoritoController = new FavoritoController();

$idUsuario = $_SESSION['id_usuario'];

$usuario = $usuarioController->buscarPorId($idUsuario);

if (!$usuario) {
    die('Usuário não encontrado.');
}

$mensagem = '';
$favoritos = $favoritoController->listarPorUsuario($idUsuario);

if (isset($_POST['salvar_perfil'])) {

    $nome = $_POST['nome'];
    $email = $_POST['email'];

    $imagem = $usuario['imagem'];

    if (
        isset($_FILES['imagem']) &&
        $_FILES['imagem']['error'] === UPLOAD_ERR_OK
    ) {

        $pasta = '../uploads/perfil/';

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $extensao = strtolower(
            pathinfo(
                $_FILES['imagem']['name'],
                PATHINFO_EXTENSION
            )
        );

        $extensoesPermitidas = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        if (!in_array($extensao, $extensoesPermitidas)) {

            $mensagem = 'Formato de imagem inválido.';

        } else {

            $nomeArquivo =
                'perfil_' .
                $idUsuario .
                '_' .
                time() .
                '.' .
                $extensao;

            $caminho = $pasta . $nomeArquivo;

            if (move_uploaded_file(
                $_FILES['imagem']['tmp_name'],
                $caminho
            )) {

                $imagem = 'uploads/perfil/' . $nomeArquivo;
            }
        }
    }

    if ($mensagem === '') {

        $dados = [
            'id_usuario' => $idUsuario,
            'nome' => $nome,
            'email' => $email,
            'imagem' => $imagem
        ];

        $usuarioController->editarPerfil($dados);

        $_SESSION['nome_usuario'] = $nome;

        $mensagem = 'Perfil atualizado com sucesso!';

        $usuario = $usuarioController->buscarPorId($idUsuario);
    }
}

if (isset($_POST['remover_favorito'])) {
    $idLocal = (int) $_POST['id_local'];
    $favoritoController->remover($idUsuario, $idLocal);
    header('Location: perfil.php');
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

    <title>Meu Perfil - ArTour</title>

    <link rel="icon" type="image/png" href="../assets/img/artour-simbolo.png">
    <link rel="stylesheet" href="../assets/css/perfil.css?v=1">
    <style>
        .favoritos-lista {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .favorito-card {
            border: 1px solid #dfe6ee;
            border-radius: 12px;
            padding: 1rem;
            background: #fff;
        }

        .favorito-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }

        .favorito-card h3 {
            margin: 0 0 0.5rem;
            font-size: 1.1rem;
        }

        .favorito-card p {
            margin: 0.25rem 0;
            color: #4a5568;
        }

        .favorito-acoes {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .favorito-acoes a,
        .favorito-acoes button {
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            border: 1px solid #dfe6ee;
            background: #f8fafc;
            color: #1f2937;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <header>

        <h1>ArTour</h1>

        <p>Meu Perfil</p>

    </header>

    <main>

        <h2>Meu Perfil</h2>

        <?php if ($mensagem !== ''): ?>

            <p>
                <?= htmlspecialchars($mensagem) ?>
            </p>

        <?php endif; ?>


        <?php if (!empty($usuario['imagem'])): ?>

            <img
                src="../<?= htmlspecialchars($usuario['imagem']) ?>"
                alt="Foto de perfil"
                width="150"
                height="150"
            >

        <?php else: ?>

            <p>
                Você ainda não possui uma foto de perfil.
            </p>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <br>

            <label for="imagem">
                Foto de perfil:
            </label>

            <br>

            <input
                type="file"
                id="imagem"
                name="imagem"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <br><br>


            <label for="nome">
                Nome:
            </label>

            <br>

            <input
                type="text"
                id="nome"
                name="nome"
                value="<?= htmlspecialchars($usuario['nome']) ?>"
                required
            >

            <br><br>


            <label for="email">
                E-mail:
            </label>

            <br>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($usuario['email']) ?>"
                required
            >

            <br><br>


            <button
                type="submit"
                name="salvar_perfil"
            >
                Salvar alterações
            </button>

        </form>

        <br>

        <section>
            <h3>Meus favoritos</h3>

            <?php if (empty($favoritos)): ?>
                <p>Você ainda não possui locais favoritados.</p>
            <?php else: ?>
                <div class="favoritos-lista">
                    <?php foreach ($favoritos as $favorito): ?>
                        <article class="favorito-card">
                            <?php if (!empty($favorito['foto'])): ?>
                                <img src="../<?= htmlspecialchars($favorito['foto']) ?>" alt="<?= htmlspecialchars($favorito['nome_local']) ?>">
                            <?php else: ?>
                                <img src="../assets/img/praca-tiradentes.jpg" alt="<?= htmlspecialchars($favorito['nome_local']) ?>">
                            <?php endif; ?>

                            <h3><?= htmlspecialchars($favorito['nome_local']) ?></h3>
                            <p><?= htmlspecialchars($favorito['nome_categoria']) ?></p>
                            <p><?= htmlspecialchars($favorito['nome_cidade']) ?> - <?= htmlspecialchars($favorito['estado']) ?></p>
                            <p><?= htmlspecialchars($favorito['endereco']) ?></p>

                            <div class="favorito-acoes">
                                <a href="local.php?id=<?= (int) $favorito['id_local'] ?>">Ver detalhes</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="remover_favorito" value="1">
                                    <input type="hidden" name="id_local" value="<?= (int) $favorito['id_local'] ?>">
                                    <button type="submit">Remover</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <br>

        <a href="../index.php">
            Voltar para o início
        </a>

        |

        <a href="logout.php">
            Sair
        </a>

    </main>

</body>

</html>
