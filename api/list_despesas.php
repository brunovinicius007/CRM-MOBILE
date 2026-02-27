<?php
// api/list_despesas.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$userId = getCurrentUserId();
$filtroData = $_GET['data'] ?? '';
$filtroCategoria = $_GET['categoria'] ?? '';

$sql = "SELECT * FROM despesas WHERE user_id = ?";
$params = [$userId];

if (!empty($filtroData)) {
    $sql .= " AND data LIKE ?";
    $params[] = "$filtroData%";
}

if (!empty($filtroCategoria)) {
    $sql .= " AND categoria = ?";
    $params[] = $filtroCategoria;
}

$sql .= " ORDER BY data DESC, id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $despesas = $stmt->fetchAll();
    sendJson($despesas);
} catch (PDOException $e) {
    sendError('Erro ao buscar despesas.');
}
