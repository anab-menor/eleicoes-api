<?php

header("Content-Type: application/json; charset=UTF-8");

$url = "https://resultados.tse.jus.br/oficial/comum/config/ele-c.json";

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
        "mensagem" => "Não foi possível consultar o TSE."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$dados = json_decode($resposta, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(502);

    echo json_encode([
        "status" => "erro",
        "mensagem" => "A resposta recebida não é um JSON válido."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

echo json_encode([
    "status" => "ok",
    "fonte" => "TSE",
    "dados" => $dados
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>