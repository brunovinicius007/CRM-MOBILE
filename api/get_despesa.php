<?php
// api/get_despesa.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$id = $_GET['id'] ?? null;
if (!$id) sendError('ID inválido.');

try {
    $stmt = $pdo->prepare("SELECT * FROM despesas WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, getCurrentUserId()]);
    $item = $stmt->fetch();
    
    if ($item) sendJson($item);
    else sendError('Item não encontrado.', 404);
} catch (PDOException $e) {
    sendError('Erro ao buscar dados.');
}
