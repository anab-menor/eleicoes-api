
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
        "mensagem" => "Falha ao consultar o TSE."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$dados = json_decode($resposta, true);

if (!is_array($dados) || !isset($dados["pl"])) {
    http_response_code(502);

    echo json_encode([
        "status" => "erro",
        "mensagem" => "Resposta inesperada do TSE."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$eleicoes = [];

foreach ($dados["pl"] as $pleito) {
    foreach ($pleito["e"] ?? [] as $eleicao) {
        $eleicoes[] = [
            "nome" => $eleicao["nm"] ?? null,
            "data" => $pleito["dt"] ?? null,
            "turno" => $eleicao["t"] ?? null,
            "cargos" => array_map(
                fn($cargo) => $cargo["ds"] ?? null,
                $eleicao["abr"][0]["cp"] ?? []
            )
        ];
    }
}

echo json_encode([
    "status" => "ok",
    "fonte" => "TSE",
    "total" => count($eleicoes),
    "eleicoes" => $eleicoes
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
