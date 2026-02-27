<?php
// api/delete_receita.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$id = $input['id'] ?? null;

if (!$id) {
    sendError('ID inválido.');
}

try {
    $stmt = $pdo->prepare("DELETE FROM receitas WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, getCurrentUserId()]);
    if ($stmt->rowCount() > 0) {
        sendJson(['message' => 'Receita removida.']);
    } else {
        sendError('Receita não encontrada ou sem permissão.', 404);
    }
} catch (PDOException $e) {
    sendError('Erro ao remover receita.');
}
