<?php
// api/list_metas.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$userId = getCurrentUserId();

try {
    // Busca as metas do usuário
    $stmt = $pdo->prepare("SELECT *, (valor_poupado / valor_objetivo * 100) as progresso FROM metas WHERE user_id = ? ORDER BY prazo ASC");
    $stmt->execute([$userId]);
    $metas = $stmt->fetchAll();
    
    // Sempre retorna um array, mesmo que vazio
    sendJson($metas ? $metas : []);
} catch (PDOException $e) {
    sendError('Erro ao buscar metas no banco de dados.');
}
