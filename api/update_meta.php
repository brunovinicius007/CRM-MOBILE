<?php
// api/update_meta.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Method Not Allowed', 405);

$input = getJsonInput();
$id = $input['id'] ?? null;
$titulo = $input['titulo'] ?? '';
$valor_objetivo = $input['valor_objetivo'] ?? '';
$prazo = $input['prazo'] ?? '';

if (!$id || empty($titulo) || empty($valor_objetivo) || empty($prazo)) {
    sendError('Preencha todos os campos corretamente.');
}

try {
    $stmt = $pdo->prepare("UPDATE metas SET titulo = ?, valor_objetivo = ?, prazo = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$titulo, $valor_objetivo, $prazo, $id, getCurrentUserId()]);
    sendJson(['message' => 'Meta atualizada com sucesso!']);
} catch (PDOException $e) {
    sendError('Erro ao atualizar meta.');
}
