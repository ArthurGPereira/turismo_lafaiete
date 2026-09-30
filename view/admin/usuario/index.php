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

require_once '../../../controller/UsuarioController.php';

$usuarioController = new UsuarioController();

$usuarios = $usuarioController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gerenciar Usuários - Tour Local</title>

</head>

<body>

    <header>

        <h1>Tour Local</h1>

        <p>Painel administrativo</p>

    </header>

    <main>

        <h2>Gerenciar Usuários</h2>

        <p>
            <a href="../index.php">
                Voltar para o painel
            </a>
        </p>

        <hr>

        <?php if (empty($usuarios)): ?>

            <p>Nenhum usuário cadastrado.</p>

        <?php else: ?>

            <table border="1" cellpadding="10">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Ações</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($usuarios as $usuario): ?>

                        <tr>

                            <td>
                                <?= $usuario['id_usuario'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($usuario['nome']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($usuario['email']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($usuario['tipo']) ?>
                            </td>

                            <td>

                                <a
                                    href="editar.php?id=<?= $usuario['id_usuario'] ?>"
                                >
                                    Editar
                                </a>

                                |

                                <a
                                    href="excluir.php?id=<?= $usuario['id_usuario'] ?>"
                                >
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </main>

    <footer>

        <p>
            <a href="../../logout.php">
                Sair
            </a>
        </p>

    </footer>

</body>

</html>
