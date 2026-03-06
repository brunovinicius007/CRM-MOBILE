<?php
// api/list_orcamentos.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

try {
    $stmt = $pdo->prepare("SELECT categoria, valor_limite FROM orcamentos WHERE user_id = ?");
    $stmt->execute([getCurrentUserId()]);
    sendJson($stmt->fetchAll());
} catch (PDOException $e) {
    sendError('Erro ao buscar orçamentos.');
}
