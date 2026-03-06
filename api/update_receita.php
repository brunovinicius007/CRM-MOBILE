<?php
// api/update_receita.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$id = $input['id'] ?? null;
$descricao = $input['descricao'] ?? '';
$valor = $input['valor'] ?? '';
$categoria = $input['categoria'] ?? '';
$data = $input['data'] ?? '';

if (!$id || empty($descricao) || empty($valor) || empty($categoria) || empty($data)) {
    sendError('Preencha todos os campos.');
}

try {
    $stmt = $pdo->prepare("UPDATE receitas SET descricao = ?, valor = ?, categoria = ?, data = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$descricao, $valor, $categoria, $data, $id, getCurrentUserId()]);
    
    if ($stmt->rowCount() > 0) {
        sendJson(['message' => 'Receita atualizada com sucesso!']);
    } else {
        sendError('Nenhuma alteração realizada ou sem permissão.', 404);
    }
} catch (PDOException $e) {
    sendError('Erro ao atualizar receita.');
}
