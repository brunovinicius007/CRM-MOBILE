<?php
// api/delete_meta.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$input = getJsonInput();
$id = $input['id'] ?? null;

if (!$id) sendError('ID inválido.');

try {
    $stmt = $pdo->prepare("DELETE FROM metas WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, getCurrentUserId()]);
    sendJson(['message' => 'Meta removida.']);
} catch (PDOException $e) {
    sendError('Erro ao remover meta.');
}
