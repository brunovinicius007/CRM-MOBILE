<?php
/**
 * src/utils.php - Funções Utilitárias Gerais
 * Auxilia na formatação de respostas e limpeza de dados.
 */

/**
 * Carrega variáveis de ambiente de um arquivo .env
 */
function loadEnv($path) {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

/**
 * Envia uma resposta em formato JSON para o frontend e encerra o script.
 */
function sendJson($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Atalho para enviar uma mensagem de erro em JSON.
 */
function sendError($message, $statusCode = 400) {
    sendJson(['error' => $message], $statusCode);
}

/**
 * Captura e decodifica o corpo de uma requisição JSON (POST/PUT).
 */
function getJsonInput() {
    $json = file_get_contents('php://input');
    return json_decode($json, true);
}

/**
 * Remove tags HTML e caracteres maliciosos de uma string (proteção contra XSS).
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
