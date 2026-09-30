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

require_once '../../../controller/CidadeController.php';

$cidadeController = new CidadeController();

$cidades = $cidadeController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gerenciar Cidades - Tour Local</title>

</head>

<body>

    <header>

        <h1>Tour Local</h1>

        <p>Painel administrativo</p>

    </header>

    <main>

        <h2>Gerenciar Cidades</h2>

        <p>
            <a href="../index.php">
                Voltar para o painel
            </a>
        </p>

        <p>
            <a href="cadastro.php">
                Cadastrar nova cidade
            </a>
        </p>

        <hr>

        <?php if (empty($cidades)): ?>

            <p>Nenhuma cidade cadastrada.</p>

        <?php else: ?>

            <table border="1" cellpadding="10">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nome</th>
                        <th>Estado</th>
                        <th>Descrição</th>
                        <th>Ações</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($cidades as $cidade): ?>

                        <tr>

                            <td>
                                <?= $cidade['id_cidade'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($cidade['nome']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($cidade['estado']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($cidade['descricao'] ?? '') ?>
                            </td>

                            <td>

                                <a
                                    href="editar.php?id=<?= $cidade['id_cidade'] ?>"
                                >
                                    Editar
                                </a>

                                |

                                <a
                                    href="excluir.php?id=<?= $cidade['id_cidade'] ?>"
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
