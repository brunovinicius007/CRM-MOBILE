<?php
// api/create_receita.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$descricao = $input['descricao'] ?? '';
$valor = $input['valor'] ?? '';
$categoria = $input['categoria'] ?? '';
$data = $input['data'] ?? date('Y-m-d');

if (empty($descricao) || empty($valor) || empty($categoria)) {
    sendError('Preencha os campos obrigatórios.');
}

try {
    $stmt = $pdo->prepare("INSERT INTO receitas (user_id, descricao, valor, categoria, data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([getCurrentUserId(), $descricao, $valor, $categoria, $data]);
    sendJson(['message' => 'Receita adicionada!']);
} catch (PDOException $e) {
    sendError('Erro ao adicionar receita.');
}
