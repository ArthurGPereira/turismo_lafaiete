<?php

header('Content-Type: application/json; charset=utf-8');

$endereco = trim($_GET['q'] ?? '');

if ($endereco === '') {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Endereço não informado.'
    ]);

    exit;
}

$url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
    'q' => $endereco,
    'format' => 'jsonv2',
    'limit' => 1,
    'countrycodes' => 'br'
]);

$contexto = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' =>
            "User-Agent: ArTour/1.0 (projeto acadêmico)\r\n" .
            "Accept: application/json\r\n",
        'timeout' => 10
    ]
]);

$resposta = @file_get_contents(
    $url,
    false,
    $contexto
);

if ($resposta === false) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Não foi possível consultar o mapa.'
    ]);

    exit;
}

$resultados = json_decode(
    $resposta,
    true
);

if (
    !is_array($resultados) ||
    empty($resultados)
) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Endereço não encontrado.'
    ]);

    exit;
}

$resultado = $resultados[0];

echo json_encode([
    'sucesso' => true,
    'latitude' => $resultado['lat'],
    'longitude' => $resultado['lon'],
    'nome' => $resultado['display_name'] ?? ''
]);
