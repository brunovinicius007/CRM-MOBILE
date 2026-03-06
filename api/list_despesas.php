<?php
/**
 * api/list_despesas.php - API de Listagem e Filtros
 * Retorna as despesas do usuário logado baseadas em filtros de data e categoria.
 */

require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$userId = getCurrentUserId();
$filtroData = $_GET['data'] ?? '';      // Formato esperado: YYYY-MM
$filtroCategoria = $_GET['categoria'] ?? '';

// Construção dinâmica da query SQL
$sql = "SELECT * FROM despesas WHERE user_id = ?";
$params = [$userId];

// Filtro por mês/ano (usando LIKE para pegar qualquer dia daquele mês)
if (!empty($filtroData)) {
    $sql .= " AND data LIKE ?";
    $params[] = $filtroData . "%";
}

// Filtro por Categoria
if (!empty($filtroCategoria)) {
    $sql .= " AND categoria = ?";
    $params[] = $filtroCategoria;
}

// Ordenação: Mais recentes primeiro
$sql .= " ORDER BY data DESC, id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $despesas = $stmt->fetchAll();
    
    // Retorna a lista para o JavaScript renderizar na tela
    sendJson($despesas);
} catch (PDOException $e) {
    sendError('Erro ao buscar a lista de despesas.');
}
