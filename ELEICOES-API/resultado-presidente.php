
<?php

header("Content-Type: application/json; charset=UTF-8");

$url = "https://resultados.tse.jus.br/oficial/ele2026/6258/dados/br/br-c0001-e006258-u.json";

$contexto = stream_context_create([
    "http" => [
        "timeout" => 15,
        "ignore_errors" => true
    ]
]);

$resposta = @file_get_contents($url, false, $contexto);

if ($resposta === false) {
    http_response_code(502);

    echo json_encode([
        "status" => "erro",
        "mensagem" => "Não foi possível consultar os resultados do TSE."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$dados = json_decode($resposta, true);

if (!is_array($dados) || !isset($dados["carg"])) {
    http_response_code(502);

    echo json_encode([
        "status" => "erro",
        "mensagem" => "O TSE retornou dados em formato inesperado."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

echo json_encode([
    "status" => "ok",
    "fonte" => "TSE",
    "eleicao" => $dados["ele"] ?? null,
    "data_atualizacao" => $dados["dg"] ?? null,
    "cargo" => $dados["carg"][0]["nmn"] ?? null,
    "candidatos" => $dados["carg"][0]["agr"] ?? [],
    "comparecimento" => $dados["e"]["c"] ?? null,
    "abstencoes" => $dados["e"]["a"] ?? null,
    "votos_validos" => $dados["v"]["vv"] ?? null,
    "votos_brancos" => $dados["v"]["vb"] ?? null,
    "votos_nulos" => $dados["v"]["tvn"] ?? null
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
