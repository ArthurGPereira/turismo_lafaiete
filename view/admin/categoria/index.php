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

require_once '../../../dao/CategoriaDAO.php';

$categoriaDAO = new CategoriaDAO();

$categorias = $categoriaDAO->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gerenciar Categorias - Turismo-Lafa</title>

</head>

<body>

    <header>

        <h1>Turismo-Lafa</h1>

        <p>
            Painel administrativo
        </p>

    </header>

    <main>

        <h2>Gerenciar Categorias</h2>

        <p>
            Olá,
            <?= htmlspecialchars($_SESSION['nome_usuario']) ?>!
        </p>

        <hr>

        <p>

            <a href="cadastro.php">
                Cadastrar categoria
            </a>

            |

            <a href="../index.php">
                Voltar para o painel
            </a>

        </p>

        <hr>

        <h3>Categorias cadastradas</h3>

        <?php if (count($categorias) > 0): ?>

            <?php foreach ($categorias as $categoria): ?>

                <section>

                    <h4>
                        <?= htmlspecialchars($categoria['nome']) ?>
                    </h4>

                    <?php if (!empty($categoria['descricao'])): ?>

                        <p>
                            <strong>Descrição:</strong>
                            <?= htmlspecialchars($categoria['descricao']) ?>
                        </p>

                    <?php endif; ?>

                    <p>

                        <a href="editar.php?id=<?= $categoria['id_categoria'] ?>">
                            Editar
                        </a>

                        |

                        <a
                            href="excluir.php?id=<?= $categoria['id_categoria'] ?>"
                            onclick="return confirm('Tem certeza que deseja excluir esta categoria?')"
                        >
                            Excluir
                        </a>

                    </p>

                    <hr>

                </section>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                Nenhuma categoria cadastrada.
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
