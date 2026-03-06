<?php
// api/create_meta.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Method Not Allowed', 405);

$input = getJsonInput();
$titulo = $input['titulo'] ?? '';
$valor_objetivo = $input['valor_objetivo'] ?? '';
$prazo = $input['prazo'] ?? '';

if (empty($titulo) || empty($valor_objetivo) || empty($prazo)) {
    sendError('Preencha todos os campos.');
}

try {
    $stmt = $pdo->prepare("INSERT INTO metas (user_id, titulo, valor_objetivo, prazo) VALUES (?, ?, ?, ?)");
    $stmt->execute([getCurrentUserId(), $titulo, $valor_objetivo, $prazo]);
    sendJson(['message' => 'Meta criada com sucesso!']);
} catch (PDOException $e) {
    sendError('Erro ao criar meta.');
}
