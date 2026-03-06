<?php
// api/update_despesa.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$id = $input['id'] ?? null;
$descricao = $input['descricao'] ?? '';
$valor = $input['valor'] ?? '';
$categoria = $input['categoria'] ?? '';
$subcategoria = $input['subcategoria'] ?? null;
$data = $input['data'] ?? '';

if (!$id || empty($descricao) || empty($valor) || empty($categoria) || empty($data)) {
    sendError('Preencha todos os campos.');
}

try {
    $stmt = $pdo->prepare("UPDATE despesas SET descricao = ?, valor = ?, categoria = ?, subcategoria = ?, data = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$descricao, $valor, $categoria, $subcategoria, $data, $id, getCurrentUserId()]);
    
    if ($stmt->rowCount() > 0) {
        sendJson(['message' => 'Despesa atualizada com sucesso!']);
    } else {
        sendError('Nenhuma alteração realizada ou sem permissão.', 404);
    }
} catch (PDOException $e) {
    sendError('Erro ao atualizar despesa.');
}
