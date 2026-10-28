
<?php

require_once 'controller/LocalController.php';
require_once 'controller/CidadeController.php';
require_once 'controller/CategoriaController.php';
require_once 'controller/EventoController.php';

$localController = new LocalController();
$cidadeController = new CidadeController();
$categoriaController = new CategoriaController();
$eventoController = new EventoController();

$locais = $localController->listar();
$cidades = $cidadeController->listar();
$categorias = $categoriaController->listar();
$eventos = $eventoController->listar();

$busca = trim($_GET['busca'] ?? '');
$cidadeSelecionada = $_GET['cidade'] ?? '';
$categoriaSelecionada = $_GET['categoria'] ?? '';

/*
|--------------------------------------------------------------------------
| FILTROS DOS LOCAIS
|--------------------------------------------------------------------------
*/

$locais = array_filter($locais, function ($local) use (
    $busca,
    $cidadeSelecionada,
    $categoriaSelecionada
) {
    if ($busca !== '') {
        $nome = mb_strtolower($local['nome'] ?? '', 'UTF-8');
        $descricao = mb_strtolower($local['descricao'] ?? '', 'UTF-8');
        $textoBusca = mb_strtolower($busca, 'UTF-8');

        if (
            mb_strpos($nome, $textoBusca, 0, 'UTF-8') === false &&
            mb_strpos($descricao, $textoBusca, 0, 'UTF-8') === false
        ) {
            return false;
        }
    }

    if (
        $cidadeSelecionada !== '' &&
        (string) ($local['id_cidade'] ?? '') !== (string) $cidadeSelecionada
    ) {
        return false;
    }

    if (
        $categoriaSelecionada !== '' &&
        (string) ($local['id_categoria'] ?? '') !== (string) $categoriaSelecionada
    ) {
        return false;
    }

    return true;
});

function escapar($valor)
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ArTour | Descubra novos lugares</title>

    <link rel="stylesheet" href="assets/css/site.css">
</head>

<body>

<!-- =========================================================
     CABEÇALHO
========================================================= -->

<header class="header">
    <div class="container">

        <div class="logo">
            <a href="index.php">ArTour</a>
        </div>

        <nav class="menu">
            <a href="index.php">Início</a>
            <a href="#categorias">Categorias</a>
            <a href="#locais">Locais</a>
            <a href="#eventos">Eventos</a>
            <a href="view/login.php" class="menu-login">Entrar</a>
        </nav>

    </div>
</header>

<main>

<!-- =========================================================
     HERO
========================================================= -->

<section class="hero" id="inicio">
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

            <form
                method="GET"
                action="index.php"
                class="search-form"
            >

                <input
                    type="text"
                    name="busca"
                    placeholder="O que você está procurando?"
                    value="<?= escapar($busca) ?>"
                >

                <input
                    type="hidden"
                    name="cidade"
                    value="<?= escapar($cidadeSelecionada) ?>"
                >

                <input
                    type="hidden"
                    name="categoria"
                    value="<?= escapar($categoriaSelecionada) ?>"
                >

                <button type="submit">Buscar</button>

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

        <form method="GET" action="index.php">

            <label for="cidade">Escolha uma cidade</label>

            <input
                type="hidden"
                name="busca"
                value="<?= escapar($busca) ?>"
            >

            <input
                type="hidden"
                name="categoria"
                value="<?= escapar($categoriaSelecionada) ?>"
            >

            <select
                name="cidade"
                id="cidade"
                onchange="this.form.submit()"
            >

                <option value="">Todas as cidades</option>

                <?php foreach ($cidades as $cidade): ?>

                    <option
                        value="<?= (int) $cidade['id_cidade'] ?>"
                        <?= (string) $cidadeSelecionada ===
                            (string) $cidade['id_cidade']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= escapar($cidade['nome']) ?>

                        <?php if (!empty($cidade['estado'])): ?>
                            - <?= escapar($cidade['estado']) ?>
                        <?php endif; ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </form>

    </div>
</section>

<!-- =========================================================
     CATEGORIAS
========================================================= -->

<section class="categories" id="categorias">
    <div class="container">

        <div class="section-header">
            <span>Encontre o que procura</span>
            <h2>Explore por categoria</h2>
        </div>

        <div class="category-grid">

            <?php if (!empty($categorias)): ?>

                <?php foreach ($categorias as $categoria): ?>

                    <?php
                    $nomeCategoria = mb_strtolower(
                        $categoria['nome'] ?? '',
                        'UTF-8'
                    );

                    $iconeCategoria = '⭐';

                    if (mb_strpos($nomeCategoria, 'restaurante') !== false) {
                        $iconeCategoria = '🍽️';
                    } elseif (
                        mb_strpos($nomeCategoria, 'turístico') !== false ||
                        mb_strpos($nomeCategoria, 'turistico') !== false
                    ) {
                        $iconeCategoria = '📍';
                    } elseif (
                        mb_strpos($nomeCategoria, 'aluguel') !== false
                    ) {
                        $iconeCategoria = '🏠';
                    } elseif (
                        mb_strpos($nomeCategoria, 'hotel') !== false
                    ) {
                        $iconeCategoria = '🏨';
                    }
                    ?>

                    <a
                        href="index.php?categoria=<?= (int) $categoria['id_categoria'] ?>#locais"
                        class="category-card"
                    >

                        <div class="category-icon">
                            <?= $iconeCategoria ?>
                        </div>

                        <h3><?= escapar($categoria['nome']) ?></h3>

                        <span>Ver opções →</span>

                    </a>

                <?php endforeach; ?>

            <?php else: ?>

                <p>Nenhuma categoria cadastrada.</p>

            <?php endif; ?>

        </div>

    </div>
</section>

<!-- =========================================================
     LOCAIS
========================================================= -->

<section class="featured" id="locais">
    <div class="container">

        <div class="section-header">
            <span>Lugares para conhecer</span>
            <h2>Descubra novos lugares</h2>
        </div>

        <?php if (empty($locais)): ?>

            <div class="empty-message">
                <p>Nenhum local encontrado.</p>
                <a href="index.php" class="place-link">Ver todos os locais</a>
            </div>

        <?php else: ?>

            <div class="places-grid">

                <?php foreach ($locais as $local): ?>

                    <article class="place-card">

                        <div class="place-image">

                            <?php if (!empty($local['foto'])): ?>

                                <img
                                    src="<?= escapar($local['foto']) ?>"
                                    alt="<?= escapar($local['nome']) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <img
                                    src="assets/img/praca-tiradentes.jpg"
                                    alt="<?= escapar($local['nome']) ?>"
                                    loading="lazy"
                                >

                            <?php endif; ?>

                        </div>

                        <div class="place-content">

                            <span class="place-category">
                                <?= escapar($local['categoria'] ?? '') ?>
                            </span>

                            <h3><?= escapar($local['nome'] ?? '') ?></h3>

                            <small>
                                <?= escapar($local['cidade'] ?? '') ?>
                            </small>

                            <p>
                                <?= escapar(
                                    $local['descricao'] ?? 'Conheça este local.'
                                ) ?>
                            </p>

                            <div class="place-address">
                                <?= escapar($local['endereco'] ?? '') ?>
                            </div>

                            <a
                                href="view/local.php?id=<?= (int) ($local['id_local'] ?? 0) ?>"
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
     EVENTOS E CALENDÁRIO
========================================================= -->

<section class="events" id="eventos">
    <div class="container">

        <div class="section-header">
            <span>Agenda ArTour</span>
            <h2>Planeje suas experiências</h2>
            <p>
                Escolha uma data e descubra os eventos disponíveis.
            </p>
        </div>

        <div class="event-calendar-layout">

            <!-- CALENDÁRIO -->
            <div class="event-calendar">

                <div class="calendar-header">

                    <button
                        type="button"
                        id="calendar-prev"
                        class="calendar-nav"
                        aria-label="Mês anterior"
                    >
                        &#10094;
                    </button>

                    <h3 id="calendar-month">Calendário</h3>

                    <button
                        type="button"
                        id="calendar-next"
                        class="calendar-nav"
                        aria-label="Próximo mês"
                    >
                        &#10095;
                    </button>

                </div>

                <div class="calendar-weekdays">
                    <span>Seg</span>
                    <span>Ter</span>
                    <span>Qua</span>
                    <span>Qui</span>
                    <span>Sex</span>
                    <span>Sáb</span>
                    <span>Dom</span>
                </div>

                <div
                    class="calendar-days"
                    id="calendar-days"
                ></div>

                <div class="calendar-legend">
                    <span class="calendar-legend-dot"></span>
                    Dias com eventos
                </div>

                <button
                    type="button"
                    id="calendar-today"
                    class="calendar-today"
                >
                    Ir para hoje
                </button>

            </div>

            <!-- RESULTADOS DA DATA -->
            <div class="calendar-results">

                <div class="calendar-results-header">
                    <span>AGENDA DO DIA</span>
                    <h3 id="selected-date-title">Eventos disponíveis</h3>
                    <p id="selected-date-description">
                        Selecione uma data no calendário.
                    </p>
                </div>

                <div id="calendar-event-results">

                    <?php if (!empty($eventos)): ?>

                        <?php foreach ($eventos as $evento): ?>

                            <?php
                            $dataInicio = !empty($evento['data_inicio'])
                                ? date('Y-m-d', strtotime($evento['data_inicio']))
                                : '';

                            $dataFim = !empty($evento['data_fim'])
                                ? date('Y-m-d', strtotime($evento['data_fim']))
                                : $dataInicio;
                            ?>

                            <article
                                class="calendar-event-card"
                                data-start="<?= escapar($dataInicio) ?>"
                                data-end="<?= escapar($dataFim) ?>"
                            >

                                <?php if (!empty($evento['imagem'])): ?>

                                    <img
                                        class="calendar-event-image"
                                        src="<?= escapar($evento['imagem']) ?>"
                                        alt="<?= escapar($evento['titulo'] ?? 'Evento') ?>"
                                        loading="lazy"
                                    >

                                <?php else: ?>

                                    <div class="calendar-event-image calendar-event-placeholder">
                                        🎉
                                    </div>

                                <?php endif; ?>

                                <div class="calendar-event-content">

                                    <h4>
                                        <?= escapar($evento['titulo'] ?? '') ?>
                                    </h4>

                                    <p>
                                        <?= nl2br(escapar($evento['descricao'] ?? '')) ?>
                                    </p>

                                    <div class="calendar-event-info">
                                        <span>📍</span>
                                        <span>
                                            <?= escapar($evento['local_evento'] ?? '') ?>
                                        </span>
                                    </div>

                                    <?php if (!empty($evento['endereco'])): ?>
                                        <div class="calendar-event-info">
                                            <span>🗺️</span>
                                            <span><?= escapar($evento['endereco']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="calendar-event-info">
                                        <span>🗓️</span>
                                        <span>
                                            <?= date('d/m/Y', strtotime($evento['data_inicio'])) ?>
                                        </span>
                                    </div>

                                    <div class="calendar-event-info">
                                        <span>🕒</span>
                                        <span>
                                            <?= date('H:i', strtotime($evento['data_inicio'])) ?>

                                            <?php if (!empty($evento['data_fim'])): ?>
                                                até <?= date('H:i', strtotime($evento['data_fim'])) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="calendar-no-events">
                            Nenhum evento ativo foi cadastrado ainda.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- =========================================================
     SOBRE O SISTEMA
========================================================= -->

<section class="about" id="sobre">
    <div class="container">

        <div class="about-image">
            <img
                src="assets/img/praca-tiradentes.jpg"
                alt="Praça e arquitetura de Conselheiro Lafaiete"
                loading="lazy"
            >
        </div>

        <div class="about-content">

            <span>Sobre o ArTour</span>

            <h2>
                Explore diferentes cidades
                em um só lugar.
            </h2>

            <p>
                O ArTour foi desenvolvido para facilitar a descoberta
                de lugares, atrações, restaurantes e outras experiências
                turísticas.
            </p>

            <p>
                Escolha uma cidade, encontre lugares interessantes e
                tenha acesso às principais informações para planejar
                sua visita.
            </p>

        </div>

    </div>
</section>

</main>

<!-- =========================================================
     RODAPÉ
========================================================= -->

<footer class="footer">

    <div class="container">

        <div class="footer-brand">
            <h3>ArTour</h3>

            <span class="footer-slogan">
                Assistente Regional de Turismo
            </span>

            <p>
                Descubra novos lugares,
                cidades e experiências.
            </p>
        </div>

        <div class="footer-links">
            <a href="index.php">Início</a>
            <a href="#categorias">Categorias</a>
            <a href="#locais">Locais</a>
            <a href="#eventos">Eventos</a>
            <a href="#sobre">Sobre</a>
        </div>

    </div>

    <div class="footer-bottom">
        <p>
            &copy; <?= date('Y') ?> ArTour.
            Todos os direitos reservados.
        </p>
    </div>

</footer>

<!-- =========================================================
     JAVASCRIPT DO CALENDÁRIO
========================================================= -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarDays = document.getElementById('calendar-days');
    const monthTitle = document.getElementById('calendar-month');
    const previousButton = document.getElementById('calendar-prev');
    const nextButton = document.getElementById('calendar-next');
    const todayButton = document.getElementById('calendar-today');
    const selectedTitle = document.getElementById('selected-date-title');
    const selectedDescription = document.getElementById('selected-date-description');
    const results = document.getElementById('calendar-event-results');

    if (!calendarDays || !monthTitle || !results) {
        return;
    }

    const eventCards = Array.from(
        results.querySelectorAll('.calendar-event-card')
    );

    const monthNames = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril',
        'Maio', 'Junho', 'Julho', 'Agosto',
        'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    const weekdayNames = [
        'domingo', 'segunda-feira', 'terça-feira',
        'quarta-feira', 'quinta-feira', 'sexta-feira',
        'sábado'
    ];

    function localToday() {
        const now = new Date();
        return new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    function dateKey(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function sameDate(first, second) {
        return dateKey(first) === dateKey(second);
    }

    const today = localToday();

    let selectedDate = new Date(today);

    /*
     * Se não houver evento hoje, seleciona o próximo evento futuro.
     * Se não houver eventos futuros, mantém a data de hoje.
     */
    if (!eventCards.some(card => {
        return card.dataset.start &&
            card.dataset.end &&
            dateKey(today) >= card.dataset.start &&
            dateKey(today) <= card.dataset.end;
    })) {
        const upcomingDates = eventCards
            .map(card => card.dataset.start)
            .filter(date => date && date >= dateKey(today))
            .sort();

        if (upcomingDates.length > 0) {
            const parts = upcomingDates[0].split('-').map(Number);
            selectedDate = new Date(parts[0], parts[1] - 1, parts[2]);
        }
    }

    let displayedMonth = new Date(
        selectedDate.getFullYear(),
        selectedDate.getMonth(),
        1
    );

    function hasEventOn(date) {
        const key = dateKey(date);

        return eventCards.some(function (card) {
            const start = card.dataset.start;
            const end = card.dataset.end || start;

            return start && end && key >= start && key <= end;
        });
    }

    function renderEvents() {
        const key = dateKey(selectedDate);

        const matchingEvents = eventCards.filter(function (card) {
            const start = card.dataset.start;
            const end = card.dataset.end || start;

            return start && end && key >= start && key <= end;
        });

        const formattedDate = selectedDate.toLocaleDateString('pt-BR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        selectedTitle.textContent =
            formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);

        selectedDescription.textContent =
            'Eventos disponíveis para ' + weekdayNames[selectedDate.getDay()] + '.';

        eventCards.forEach(function (card) {
            card.hidden = !matchingEvents.includes(card);
        });

        let message = results.querySelector('.calendar-no-events');

        if (matchingEvents.length === 0) {
            if (!message) {
                message = document.createElement('p');
                message.className = 'calendar-no-events';
                results.appendChild(message);
            }

            message.textContent =
                'Não há eventos cadastrados para esta data. Escolha outro dia no calendário.';
            message.hidden = false;
        } else if (message) {
            message.hidden = true;
        }
    }

    function renderCalendar() {
        calendarDays.replaceChildren();

        monthTitle.textContent =
            monthNames[displayedMonth.getMonth()] +
            ' ' +
            displayedMonth.getFullYear();

        const firstDay = new Date(
            displayedMonth.getFullYear(),
            displayedMonth.getMonth(),
            1
        );

        const mondayOffset = (firstDay.getDay() + 6) % 7;

        const gridStart = new Date(firstDay);
        gridStart.setDate(firstDay.getDate() - mondayOffset);

        for (let index = 0; index < 42; index++) {
            const date = new Date(gridStart);
            date.setDate(gridStart.getDate() + index);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'calendar-day';
            button.textContent = date.getDate();

            button.setAttribute(
                'aria-label',
                date.toLocaleDateString('pt-BR')
            );

            if (date.getMonth() !== displayedMonth.getMonth()) {
                button.classList.add('is-outside');
            }

            if (sameDate(date, today)) {
                button.classList.add('is-today');
            }

            if (sameDate(date, selectedDate)) {
                button.classList.add('is-selected');
            }

            if (hasEventOn(date)) {
                button.classList.add('has-events');
            }

            button.setAttribute(
                'aria-pressed',
                sameDate(date, selectedDate) ? 'true' : 'false'
            );

            button.addEventListener('click', function () {
                selectedDate = new Date(date);

                displayedMonth = new Date(
                    date.getFullYear(),
                    date.getMonth(),
                    1
                );

                renderCalendar();
                renderEvents();
            });

            calendarDays.appendChild(button);
        }
    }

    previousButton.addEventListener('click', function () {
        displayedMonth = new Date(
            displayedMonth.getFullYear(),
            displayedMonth.getMonth() - 1,
            1
        );

        renderCalendar();
    });

    nextButton.addEventListener('click', function () {
        displayedMonth = new Date(
            displayedMonth.getFullYear(),
            displayedMonth.getMonth() + 1,
            1
        );

        renderCalendar();
    });

    todayButton.addEventListener('click', function () {
        selectedDate = new Date(today);

        displayedMonth = new Date(
            today.getFullYear(),
            today.getMonth(),
            1
        );

        renderCalendar();
        renderEvents();
    });

    renderCalendar();
    renderEvents();
});
</script>

</body>
</html>
