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

require_once '../../../controller/LocalController.php';

$localController = new LocalController();

$locais = $localController->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gerenciar Locais - Turismo-Lafa</title>

</head>

<body>

    <header>

        <h1>Turismo-Lafa</h1>

        <p>
            Painel administrativo
        </p>

    </header>

    <main>

        <h2>Gerenciar Locais</h2>

        <p>
            Olá,
            <?= htmlspecialchars($_SESSION['nome_usuario']) ?>!
        </p>

        <hr>

        <p>

            <a href="cadastro.php">
                Cadastrar novo local
            </a>

            |

            <a href="../index.php">
                Voltar para o painel
            </a>

        </p>

        <hr>

        <h3>Locais cadastrados</h3>

        <?php if (count($locais) > 0): ?>

            <?php foreach ($locais as $local): ?>

                <section>

                    <h4>
                        <?= htmlspecialchars($local['nome']) ?>
                    </h4>

                    <p>
                        <strong>Categoria:</strong>
                        <?= htmlspecialchars($local['categoria']) ?>
                    </p>

                    <p>
                        <strong>Cidade:</strong>
                        <?= htmlspecialchars($local['cidade']) ?>
                    </p>

                    <p>
                        <strong>Endereço:</strong>
                        <?= htmlspecialchars($local['endereco']) ?>
                    </p>

                    <?php if (!empty($local['descricao'])): ?>

                        <p>
                            <strong>Descrição:</strong>
                            <?= htmlspecialchars($local['descricao']) ?>
                        </p>

                    <?php endif; ?>

                    <p>

                        <a
                            href="editar.php?id=<?= $local['id_local'] ?>"
                        >
                            Editar
                        </a>

                        |

                        <a
                            href="excluir.php?id=<?= $local['id_local'] ?>"
                            onclick="return confirm('Tem certeza que deseja excluir este local?')"
                        >
                            Excluir
                        </a>

                    </p>

                    <hr>

                </section>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                Nenhum local cadastrado.
            </p>

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
