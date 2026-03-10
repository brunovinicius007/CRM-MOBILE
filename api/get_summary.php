<?php
// api/get_summary.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();
$userId = getCurrentUserId();

$mesAtual = date('Y-m');

try {
    // Total de Receitas do Mês
    $stmt = $pdo->prepare("SELECT SUM(valor) as total FROM receitas WHERE user_id = ? AND data LIKE ?");
    $stmt->execute([$userId, "$mesAtual%"]);
    $totalReceitas = $stmt->fetch()['total'] ?? 0;

    // Total de Despesas do Mês
    $stmt = $pdo->prepare("SELECT SUM(valor) as total FROM despesas WHERE user_id = ? AND data LIKE ?");
    $stmt->execute([$userId, "$mesAtual%"]);
    $totalDespesas = $stmt->fetch()['total'] ?? 0;

    $saldo = $totalReceitas - $totalDespesas;

    sendJson([
        'saldo' => (float)$saldo,
        'receitas' => (float)$totalReceitas,
        'despesas' => (float)$totalDespesas,
        'mes' => $mesAtual
    ]);
} catch (PDOException $e) {
    sendError('Erro ao buscar resumo.');
}
