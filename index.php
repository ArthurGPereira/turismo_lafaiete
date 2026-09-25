<?php

require_once 'dao/LocalDAO.php';
require_once 'controller/LocalController.php';

$localController = new LocalController();


// CADASTRAR LOCAL
if (isset($_POST['cadastrar_local'])) {

    $dados = [
        'id_cidade' => 1,
        'id_categoria' => $_POST['id_categoria'],
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'],
        'endereco' => $_POST['endereco'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'horario_funcionamento' => $_POST['horario_funcionamento'],
        'preco' => $_POST['preco'] !== '' ? $_POST['preco'] : null,
        'latitude' => null,
        'longitude' => null,
        'status' => 'ativo'
    ];

    $localController->cadastrar($dados);

    header('Location: index.php');
    exit;
}


// EDITAR LOCAL
if (isset($_POST['editar_local'])) {

    $dados = [
        'id_local' => $_POST['id_local'],
        'id_categoria' => $_POST['id_categoria'],
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'],
        'endereco' => $_POST['endereco'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'horario_funcionamento' => $_POST['horario_funcionamento'],
        'preco' => $_POST['preco'] !== '' ? $_POST['preco'] : null
    ];

    $localController->editar($dados);

    header('Location: index.php');
    exit;
}
// EXCLUIR LOCAL
if (isset($_GET['excluir'])) {

    $id = $_GET['excluir'];

    $localController->excluir($id);

    header('Location: index.php');
    exit;
}


// LISTAR LOCAIS
$locais = $localController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Turismo-Lafa</title>

</head>

<body>

    <h1>Turismo-Lafa</h1>
<p>
    <a href="view/cadastro_usuario.php">
        Cadastrar usuário
    </a>
</p>

<p>
    <a href="view/login.php">
        Login
    </a>
</p>
    <p>
        <a href="view/cadastro_local.php">
            Cadastrar novo local
        </a>
    </p>

    <h2>Locais cadastrados</h2>

    <?php if (count($locais) > 0): ?>

        <?php foreach ($locais as $local): ?>

            <div>

                <h3>
                    <?= htmlspecialchars($local['nome']) ?>
                </h3>

                <p>
                    Categoria:
                    <?= htmlspecialchars($local['categoria']) ?>
                </p>

                <p>
                    Cidade:
                    <?= htmlspecialchars($local['cidade']) ?>
                </p>

                <p>
                    Endereço:
                    <?= htmlspecialchars($local['endereco']) ?>
                </p>

                <p>
                    <?= htmlspecialchars($local['descricao']) ?>
                </p>

                <p>

    <a href="view/editar_local.php?id=<?= $local['id_local'] ?>">
        Editar
    </a>

    |

    <a
        href="index.php?excluir=<?= $local['id_local'] ?>"
        onclick="return confirm('Tem certeza que deseja excluir este local?')"
    >
        Excluir
    </a>

</p>

                <hr>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>Nenhum local cadastrado.</p>

    <?php endif; ?>

</body>

</html>
