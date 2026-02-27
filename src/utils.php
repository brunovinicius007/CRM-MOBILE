<?php
// src/utils.php

function sendJson($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function sendError($message, $statusCode = 400) {
    sendJson(['error' => $message], $statusCode);
}

function getJsonInput() {
    $json = file_get_contents('php://input');
    return json_decode($json, true);
}

function sanitize($data) {
    return htmlspecialchars(strip_tags($data));
}
