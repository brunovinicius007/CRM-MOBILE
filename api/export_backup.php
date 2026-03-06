<?php
// api/export_backup.php
require_once '../src/db.php';
require_once '../src/auth.php';

requireAuth();

$userId = getCurrentUserId();
$backupData = [];

try {
    // Busca Despesas
    $stmt = $pdo->prepare("SELECT descricao, valor, categoria, subcategoria, data FROM despesas WHERE user_id = ?");
    $stmt->execute([$userId]);
    $backupData['despesas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca Receitas
    $stmt = $pdo->prepare("SELECT descricao, valor, categoria, data FROM receitas WHERE user_id = ?");
    $stmt->execute([$userId]);
    $backupData['receitas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca Metas
    $stmt = $pdo->prepare("SELECT titulo, valor_objetivo, valor_poupado, prazo FROM metas WHERE user_id = ?");
    $stmt->execute([$userId]);
    $backupData['metas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca Orçamentos
    $stmt = $pdo->prepare("SELECT categoria, valor_limite FROM orcamentos WHERE user_id = ?");
    $stmt->execute([$userId]);
    $backupData['orcamentos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $backupData['gerado_em'] = date('Y-m-d H:i:s');
    $backupData['app'] = 'Minhas Finanças';

    // Força o download do arquivo JSON
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="minhas_financas_backup_' . date('Y-m-d') . '.json"');
    echo json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao gerar backup.']);
}
