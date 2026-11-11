<?php

declare(strict_types=1);

const IBGE_ESTADOS_URL = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados';
const IBGE_SQL_OUTPUT = __DIR__ . '/municipios_ibge_importacao.sql';
const BATCH_SIZE = 500;

function safeTrim($value): string
{
    return trim((string) $value);
}

function escapeSqlString(string $value): string
{
    return str_replace("'", "''", $value);
}

function fetchJson(string $url): array
{
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'ArTour-IBGE-Import/1.0',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8'
            ]
        ]);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false) {
            throw new RuntimeException('Falha ao consultar a API do IBGE via cURL: ' . $curlError);
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException('A API do IBGE respondeu com erro HTTP ' . $statusCode . '.');
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('A API do IBGE respondeu com conteúdo inválido.');
        }

        return $decoded;
    }

    $streamContext = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8\r\nUser-Agent: ArTour-IBGE-Import/1.0\r\n",
            'timeout' => 30,
        ],
        'https' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8\r\nUser-Agent: ArTour-IBGE-Import/1.0\r\n",
            'timeout' => 30,
        ],
    ]);

    $response = @file_get_contents($url, false, $streamContext);
    if ($response === false) {
        throw new RuntimeException('Falha ao consultar a API do IBGE via file_get_contents.');
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('A API do IBGE respondeu com conteúdo inválido.');
    }

    return $decoded;
}

function buildSqlContent(array $municipios): string
{
    $lines = [];
    $lines[] = '-- Arquivo gerado automaticamente pela ferramenta temporária de importação do IBGE.';
    $lines[] = '-- Não foi executado SQL no banco de dados neste processo.';
    $lines[] = '-- Data da geração: ' . date('d/m/Y H:i:s');
    $lines[] = '';
    $lines[] = 'SET NAMES utf8mb4;';
    $lines[] = 'SET character_set_client = utf8mb4;';
    $lines[] = 'SET character_set_connection = utf8mb4;';
    $lines[] = 'SET character_set_results = utf8mb4;';
    $lines[] = '';
    $lines[] = 'CREATE TABLE IF NOT EXISTS municipios_ibge_importacao (';
    $lines[] = '    nome VARCHAR(100) NOT NULL,';
    $lines[] = '    estado VARCHAR(100) NOT NULL';
    $lines[] = ') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';
    $lines[] = '';
    $lines[] = 'DELETE FROM municipios_ibge_importacao;';
    $lines[] = '';
    $lines[] = 'INSERT INTO municipios_ibge_importacao (nome, estado) VALUES';

    $rows = [];
    foreach ($municipios as $municipio) {
        $nome = escapeSqlString(safeTrim($municipio['nome'] ?? ''));
        $estado = escapeSqlString(safeTrim($municipio['estado'] ?? ''));

        if ($nome === '' || $estado === '') {
            continue;
        }

        $rows[] = "('" . $nome . "', '" . $estado . "')";
    }

    if (empty($rows)) {
        $lines[] = ';';
        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    $batches = array_chunk($rows, BATCH_SIZE);
    foreach ($batches as $index => $batch) {
        $lines[] = implode(",\n", $batch) . ($index === array_key_last($batches) ? ';' : ',');
    }

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

function renderPage(string $status, string $message, array $details): void
{
    $title = $status === 'success' ? 'Importação do IBGE concluída' : 'Erro na importação do IBGE';
    $detailsHtml = '';

    foreach ($details as $detail) {
        $detailsHtml .= '<li>' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') . '</li>';
    }

    echo '<!DOCTYPE html>' . PHP_EOL;
    echo '<html lang="pt-BR">' . PHP_EOL;
    echo '<head>' . PHP_EOL;
    echo '  <meta charset="UTF-8">' . PHP_EOL;
    echo '  <meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL;
    echo '  <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>' . PHP_EOL;
    echo '  <style>' . PHP_EOL;
    echo '    body { font-family: Arial, sans-serif; background: #f6f8fb; color: #1f2937; margin: 0; padding: 32px; }' . PHP_EOL;
    echo '    .box { max-width: 850px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06); }' . PHP_EOL;
    echo '    .status { font-size: 1.4rem; font-weight: 700; margin-bottom: 12px; }' . PHP_EOL;
    echo '    .success { color: #0f766e; }' . PHP_EOL;
    echo '    .error { color: #b91c1c; }' . PHP_EOL;
    echo '    ul { margin-top: 16px; }' . PHP_EOL;
    echo '    code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }' . PHP_EOL;
    echo '  </style>' . PHP_EOL;
    echo '</head>' . PHP_EOL;
    echo '<body>' . PHP_EOL;
    echo '  <div class="box">' . PHP_EOL;
    echo '    <div class="status ' . ($status === 'success' ? 'success' : 'error') . '">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>' . PHP_EOL;
    echo '    <p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>' . PHP_EOL;

    if (!empty($details)) {
        echo '    <ul>' . PHP_EOL . $detailsHtml . '    </ul>' . PHP_EOL;
    }

    echo '  </div>' . PHP_EOL;
    echo '</body>' . PHP_EOL;
    echo '</html>' . PHP_EOL;
}

try {
    $estados = fetchJson(IBGE_ESTADOS_URL);
    if (!is_array($estados) || count($estados) !== 27) {
        throw new RuntimeException('A API do IBGE não retornou todas as 27 unidades da Federação.');
    }

    $municipios = [];
    $totalMunicipios = 0;

    foreach ($estados as $estado) {
        $nomeEstado = safeTrim($estado['nome'] ?? '');
        $siglaEstado = safeTrim($estado['sigla'] ?? '');
        $idEstado = (int) ($estado['id'] ?? 0);

        if ($nomeEstado === '' || $siglaEstado === '' || $idEstado <= 0) {
            throw new RuntimeException('Identificação inválida de um estado retornado pela API do IBGE.');
        }

        $urlMunicipios = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/' . rawurlencode((string) $idEstado) . '/municipios';
        $municipiosEstado = fetchJson($urlMunicipios);

        if (!is_array($municipiosEstado)) {
            throw new RuntimeException('Não foi possível consultar os municípios do estado ' . $nomeEstado . '.');
        }

        foreach ($municipiosEstado as $municipio) {
            $nomeMunicipio = safeTrim($municipio['nome'] ?? '');
            if ($nomeMunicipio === '') {
                continue;
            }

            $municipios[] = [
                'nome' => $nomeMunicipio,
                'estado' => $nomeEstado,
            ];
        }

        $totalMunicipios += count($municipiosEstado);
    }

    if ($totalMunicipios < 5000) {
        throw new RuntimeException('A API do IBGE retornou menos de 5.000 municípios. Total obtido: ' . $totalMunicipios . '.');
    }

    $sqlConteudo = buildSqlContent($municipios);
    $resultado = file_put_contents(IBGE_SQL_OUTPUT, $sqlConteudo, LOCK_EX);

    if ($resultado === false) {
        throw new RuntimeException('Não foi possível gravar o arquivo SQL em ' . IBGE_SQL_OUTPUT . '.');
    }

    $details = [
        'Estados encontrados: ' . count($estados),
        'Municípios coletados: ' . $totalMunicipios,
        'Arquivo gerado: ' . IBGE_SQL_OUTPUT,
        'Acesso: http://localhost/Turismo-Lafa/tools/importar_municipios_ibge.php'
    ];

    renderPage('success', 'Geração concluída com sucesso. O arquivo SQL foi gerado sem alterar o banco de dados.', $details);
    exit;
} catch (Throwable $erro) {
    renderPage('error', 'Ocorreu um erro durante a geração do arquivo SQL.', [
        'Mensagem: ' . $erro->getMessage(),
        'Dica: verifique a conectividade com a API do IBGE e tente novamente.'
    ]);
    exit;
}
