<?php
// api/update_meta_valor.php
// API rápida para adicionar valor à meta
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$input = getJsonInput();
$id = $input['id'] ?? null;
$valor = $input['valor'] ?? 0;

if (!$id || $valor <= 0) sendError('Dados inválidos.');

try {
    $stmt = $pdo->prepare("UPDATE metas SET valor_poupado = valor_poupado + ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$valor, $id, getCurrentUserId()]);
    sendJson(['message' => 'Valor adicionado à meta!']);
} catch (PDOException $e) {
    sendError('Erro ao atualizar meta.');
}
