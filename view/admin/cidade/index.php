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

    <title>Cidades - Tour Local</title>

<link
    rel="stylesheet"
    href="/Turismo-Lafa/assets/css/admin.css"
>

<link
    rel="stylesheet"
    href="/Turismo-Lafa/assets/css/cidade.css"
>

</head>

<body>

<header>

    <h1>Tour Local</h1>

    <p>Painel administrativo</p>

</header>


<main class="cidade-lista">

    <div class="cidade-lista-topo">

        <div>

            <div class="cidade-breadcrumb">

                <a href="../index.php">
                    Painel
                </a>

                <span>›</span>

                <span>Cidades</span>

            </div>


            <h2>
                Cidades
            </h2>


            <p>
                Explore e gerencie os destinos disponíveis no Tour Local.
            </p>

        </div>


        <a
            href="cadastro.php"
            class="cidade-btn-nova"
        >
            Adicionar cidade
        </a>

    </div>


    <?php if (empty($cidades)): ?>

        <div class="cidade-vazia">

            <h3>
                Nenhuma cidade cadastrada
            </h3>

            <p>
                Comece adicionando o primeiro destino ao Tour Local.
            </p>

            <a
                href="cadastro.php"
                class="cidade-vazia-link"
            >
                Adicionar primeira cidade
            </a>

        </div>

    <?php else: ?>


        <div class="cidade-grid">

            <?php foreach ($cidades as $cidade): ?>

                <article class="cidade-card">


                    <div class="cidade-imagem">

                        <?php if (!empty($cidade['imagem'])): ?>

                            <img
                                src="../../../<?= htmlspecialchars($cidade['imagem']) ?>"
                                alt="<?= htmlspecialchars($cidade['nome']) ?>"
                            >

                        <?php else: ?>

                            <div class="cidade-sem-imagem">
                                <span>Sem imagem</span>
                            </div>

                        <?php endif; ?>

                    </div>


                    <div class="cidade-card-conteudo">

                        <span class="cidade-estado">

                            <?= htmlspecialchars($cidade['estado']) ?>

                        </span>


                        <h3>

                            <?= htmlspecialchars($cidade['nome']) ?>

                        </h3>


                        <p>

                            <?= htmlspecialchars(
                                $cidade['descricao'] ?? 'Sem descrição.'
                            ) ?>

                        </p>


                        <div class="cidade-card-footer">

                            <a
                                href="editar.php?id=<?= (int) $cidade['id_cidade'] ?>"
                                class="cidade-editar"
                            >
                                Editar
                            </a>


                            <a
                                href="excluir.php?id=<?= (int) $cidade['id_cidade'] ?>"
                                class="cidade-excluir"
                                onclick="return confirm('Tem certeza que deseja excluir esta cidade?');"
                            >
                                Excluir
                            </a>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>


    <?php endif; ?>


    <a
        href="../index.php"
        class="cidade-voltar"
    >
        ← Voltar para o painel
    </a>


</main>

</body>

</html>
