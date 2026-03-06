<?php
// api/list_receitas.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$userId = getCurrentUserId();
$filtroData = $_GET['data'] ?? ''; // Ex: 2024-03
$filtroCategoria = $_GET['categoria'] ?? '';

$sql = "SELECT * FROM receitas WHERE user_id = ?";
$params = [$userId];

if (!empty($filtroData)) {
    // Garante que pegamos todos os dias do mês (YYYY-MM-DD)
    $sql .= " AND data LIKE ?";
    $params[] = $filtroData . "%";
}

if (!empty($filtroCategoria)) {
    $sql .= " AND categoria = ?";
    $params[] = $filtroCategoria;
}

$sql .= " ORDER BY data DESC, id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $receitas = $stmt->fetchAll();
    sendJson($receitas);
} catch (PDOException $e) {
    sendError('Erro ao buscar receitas.');
}
