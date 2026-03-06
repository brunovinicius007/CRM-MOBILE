<?php
// api/create_receita.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$descricao = $input['descricao'] ?? '';
$valor = $input['valor'] ?? '';
$categoria = $input['categoria'] ?? '';
$data_inicial = $input['data'] ?? date('Y-m-d');
$repetir_meses = (int)($input['repetir_meses'] ?? 1);

if (empty($descricao) || empty($valor) || empty($categoria)) {
    sendError('Preencha os campos obrigatórios.');
}

if ($repetir_meses < 1) $repetir_meses = 1;
if ($repetir_meses > 24) $repetir_meses = 24;

try {
    $userId = getCurrentUserId();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO receitas (user_id, descricao, valor, categoria, data) VALUES (?, ?, ?, ?, ?)");

    for ($i = 0; $i < $repetir_meses; $i++) {
        $data_atual = date('Y-m-d', strtotime("+$i month", strtotime($data_inicial)));
        $stmt->execute([$userId, $descricao, $valor, $categoria, $data_atual]);
    }

    $pdo->commit();

    $msg = ($repetir_meses > 1) 
        ? "Receita criada e repetida por $repetir_meses meses!" 
        : "Receita adicionada com sucesso!";

    sendJson(['message' => $msg]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    sendError('Erro ao adicionar receita(s).');
}
