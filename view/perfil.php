<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once '../controller/UsuarioController.php';

$usuarioController = new UsuarioController();

$idUsuario = $_SESSION['id_usuario'];

$usuario = $usuarioController->buscarPorId($idUsuario);

if (!$usuario) {
    die('Usuário não encontrado.');
}

$mensagem = '';

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

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Meu Perfil - Tour Local</title>

</head>

<body>

    <header>

        <h1>Tour Local</h1>

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
