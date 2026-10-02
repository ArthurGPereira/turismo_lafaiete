<?php

require_once 'controller/LocalController.php';
require_once 'controller/CidadeController.php';
require_once 'controller/CategoriaController.php';

$localController = new LocalController();
$cidadeController = new CidadeController();
$categoriaController = new CategoriaController();

$locais = $localController->listar();
$cidades = $cidadeController->listar();
$categorias = $categoriaController->listar();

$busca = $_GET['busca'] ?? '';
$cidadeSelecionada = $_GET['cidade'] ?? '';
$categoriaSelecionada = $_GET['categoria'] ?? '';

/*
|--------------------------------------------------------------------------
| FILTROS
|--------------------------------------------------------------------------
*/

$locais = array_filter($locais, function ($local) use (
    $busca,
    $cidadeSelecionada,
    $categoriaSelecionada
) {

    // Busca por nome ou descrição
    if ($busca !== '') {

        $nome = strtolower($local['nome']);
        $descricao = strtolower($local['descricao'] ?? '');
        $textoBusca = strtolower($busca);

        if (
            strpos($nome, $textoBusca) === false &&
            strpos($descricao, $textoBusca) === false
        ) {
            return false;
        }
    }

    // Filtro por cidade
    if (
        $cidadeSelecionada !== '' &&
        $local['id_cidade'] != $cidadeSelecionada
    ) {
        return false;
    }

    // Filtro por categoria
    if (
        $categoriaSelecionada !== '' &&
        $local['id_categoria'] != $categoriaSelecionada
    ) {
        return false;
    }

    return true;
});

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tour Local | Descubra novos lugares</title>

    <link
        rel="stylesheet"
        href="assets/css/site.css"
    >

</head>

<body>


<!-- =========================================================
     CABEÇALHO
========================================================= -->

<header class="header">

    <div class="container">

        <div class="logo">

            <a href="index.php">
                Tour Local
            </a>

        </div>


        <nav class="menu">

            <a href="index.php">
                Início
            </a>

            <a href="#categorias">
                Categorias
            </a>

            <a href="#locais">
                Locais
            </a>

            <a href="#eventos">
                Eventos
            </a>

        </nav>

    </div>

</header>


<!-- =========================================================
     HERO
========================================================= -->

<section class="hero">

    <div class="container">

        <div class="hero-content">

            <span class="hero-label">
                Explore novos destinos
            </span>


            <h1>
                Descubra lugares
                que você ainda
                não conhece.
            </h1>


            <p>
                Encontre pontos turísticos, restaurantes,
                locais para aluguel e experiências em
                diferentes cidades.
            </p>


            <!-- BUSCA -->

            <form
                method="GET"
                action="index.php"
                class="search-form"
            >

                <input
                    type="text"
                    name="busca"
                    placeholder="O que você está procurando?"
                    value="<?= htmlspecialchars($busca) ?>"
                >

                <button type="submit">
                    Buscar
                </button>

            </form>

        </div>


        <div class="hero-image">

            <img
                src="assets/img/praca-tiradentes.jpg"
                alt="Destino turístico"
            >

        </div>

    </div>

</section>


<!-- =========================================================
     FILTRO POR CIDADE
========================================================= -->

<section class="city-filter">

    <div class="container">

        <form
            method="GET"
            action="index.php"
        >

            <label for="cidade">
                Escolha uma cidade
            </label>


            <select
                name="cidade"
                id="cidade"
                onchange="this.form.submit()"
            >

                <option value="">
                    Todas as cidades
                </option>


                <?php foreach ($cidades as $cidade): ?>

                    <option
                        value="<?= $cidade['id_cidade'] ?>"
                        <?= $cidadeSelecionada == $cidade['id_cidade'] ? 'selected' : '' ?>
                    >

                        <?= htmlspecialchars($cidade['nome']) ?>

                        -

                        <?= htmlspecialchars($cidade['estado']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </form>

    </div>

</section>


<!-- =========================================================
     CATEGORIAS
========================================================= -->

<section
    class="categories"
    id="categorias"
>

    <div class="container">

        <div class="section-header">

            <span>
                Encontre o que procura
            </span>

            <h2>
                Explore por categoria
            </h2>

        </div>


        <div class="category-grid">

            <?php foreach ($categorias as $categoria): ?>

                <a
                    href="index.php?categoria=<?= $categoria['id_categoria'] ?>"
                    class="category-card"
                >

                    <div class="category-icon">

                        <?php

                        $nomeCategoria = strtolower(
                            $categoria['nome']
                        );

                        if (
                            strpos(
                                $nomeCategoria,
                                'restaurante'
                            ) !== false
                        ) {

                            echo '🍽️';

                        } elseif (
                            strpos(
                                $nomeCategoria,
                                'turístico'
                            ) !== false ||
                            strpos(
                                $nomeCategoria,
                                'turistico'
                            ) !== false
                        ) {

                            echo '📍';

                        } elseif (
                            strpos(
                                $nomeCategoria,
                                'aluguel'
                            ) !== false
                        ) {

                            echo '🏠';

                        } else {

                            echo '⭐';

                        }

                        ?>

                    </div>


                    <h3>

                        <?= htmlspecialchars(
                            $categoria['nome']
                        ) ?>

                    </h3>


                    <span>
                        Ver opções →
                    </span>

                </a>

            <?php endforeach; ?>

        </div>

    </div>

</section>


<!-- =========================================================
     LOCAIS
========================================================= -->

<section
    class="featured"
    id="locais"
>

    <div class="container">

        <div class="section-header">

            <span>
                Lugares para conhecer
            </span>

            <h2>
                Descubra novos lugares
            </h2>

        </div>


        <?php if (empty($locais)): ?>

            <div class="empty-message">

                <p>
                    Nenhum local encontrado.
                </p>

            </div>

        <?php else: ?>


            <div class="places-grid">


                <?php foreach ($locais as $local): ?>


                    <article class="place-card">


                        <!-- IMAGEM -->

                        <div class="place-image">

                            <?php if (!empty($local['foto'])): ?>

                                <img
                                    src="<?= htmlspecialchars($local['foto']) ?>"
                                    alt="<?= htmlspecialchars($local['nome']) ?>"
                                >

                            <?php else: ?>

                                <img
                                    src="assets/img/praca-tiradentes.jpg"
                                    alt="<?= htmlspecialchars($local['nome']) ?>"
                                >

                            <?php endif; ?>

                        </div>


                        <!-- CONTEÚDO -->

                        <div class="place-content">


                            <span class="place-category">

                                <?= htmlspecialchars(
                                    $local['categoria']
                                ) ?>

                            </span>


                            <h3>

                                <?= htmlspecialchars(
                                    $local['nome']
                                ) ?>

                            </h3>


                            <small>

                                <?= htmlspecialchars(
                                    $local['cidade']
                                ) ?>

                            </small>


                            <p>

                                <?= htmlspecialchars(
                                    $local['descricao']
                                    ?? 'Conheça este local.'
                                ) ?>

                            </p>


                            <div class="place-address">

                                <?= htmlspecialchars(
                                    $local['endereco']
                                ) ?>

                            </div>


                            <a
                                href="#"
                                class="place-link"
                            >
                                Conhecer lugar →
                            </a>


                        </div>

                    </article>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </div>

</section>


<!-- =========================================================
     EVENTOS
========================================================= -->

<section
    class="events"
    id="eventos"
>

    <div class="container">

        <div class="section-header">

            <span>
                Agenda
            </span>

            <h2>
                Eventos
            </h2>

        </div>


        <div class="events-empty">

            <h3>
                Em breve
            </h3>

            <p>
                Estamos preparando uma área para
                você encontrar eventos e atrações
                acontecendo nas cidades cadastradas.
            </p>

        </div>

    </div>

</section>


<!-- =========================================================
     SOBRE O SISTEMA
========================================================= -->

<section class="about">

    <div class="container">


        <div class="about-content">

            <span>
                Sobre o Tour Local
            </span>


            <h2>
                Explore diferentes cidades
                em um só lugar.
            </h2>


            <p>
                O Tour Local foi desenvolvido para
                facilitar a descoberta de lugares,
                atrações, restaurantes e outras
                experiências turísticas.
            </p>


            <p>
                Escolha uma cidade, encontre lugares
                interessantes e tenha acesso às
                principais informações para planejar
                sua visita.
            </p>

        </div>


        <div class="about-image">

            <img
                src="assets/img/praca-tiradentes.jpg"
                alt="Turismo"
            >

        </div>


    </div>

</section>


<!-- =========================================================
     RODAPÉ
========================================================= -->

<footer class="footer">

    <div class="container">


        <div class="footer-brand">

            <h3>
                Tour Local
            </h3>


            <p>
                Descubra novos lugares,
                cidades e experiências.
            </p>

        </div>


        <div class="footer-links">

            <a href="index.php">
                Início
            </a>

            <a href="#categorias">
                Categorias
            </a>

            <a href="#locais">
                Locais
            </a>

            <a href="#eventos">
                Eventos
            </a>

        </div>


    </div>


    <div class="footer-bottom">

        <p>
            © <?= date('Y') ?> Tour Local.
            Todos os direitos reservados.
        </p>

    </div>

</footer>


</body>

</html>
