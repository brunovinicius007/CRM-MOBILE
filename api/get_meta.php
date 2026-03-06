<?php
// api/get_meta.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$id = $_GET['id'] ?? null;
if (!$id) sendError('ID inválido.');

try {
    $stmt = $pdo->prepare("SELECT * FROM metas WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, getCurrentUserId()]);
    $meta = $stmt->fetch();
    
    if ($meta) sendJson($meta);
    else sendError('Meta não encontrada.', 404);
} catch (PDOException $e) {
    sendError('Erro ao buscar dados.');
}
