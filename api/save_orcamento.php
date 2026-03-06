<?php
// api/save_orcamento.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

$input = getJsonInput();
$categoria = $input['categoria'] ?? '';
$valor = $input['valor'] ?? 0;

if (empty($categoria) || $valor <= 0) sendError('Dados inválidos.');

try {
    // Upsert (Insere ou Atualiza se já existir a categoria para o usuário)
    $stmt = $pdo->prepare("INSERT INTO orcamentos (user_id, categoria, valor_limite) 
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE valor_limite = VALUES(valor_limite)");
    $stmt->execute([getCurrentUserId(), $categoria, $valor]);
    sendJson(['message' => 'Orçamento definido com sucesso!']);
} catch (PDOException $e) {
    sendError('Erro ao salvar orçamento.');
}
