<?php
/**
 * api/create_despesa.php - API para Criação de Despesas
 * Gerencia a inserção de gastos, incluindo recorrência mensal e subcategorias.
 */

require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

// Bloqueia acesso se o usuário não estiver logado
requireAuth();

// Garante que apenas requisições POST sejam aceitas
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

// Obtém os dados enviados via JSON
$input = getJsonInput();
$descricao = sanitize($input['descricao'] ?? '');
$valor = (float)($input['valor'] ?? 0);
$categoria = sanitize($input['categoria'] ?? '');
$subcategoria = isset($input['subcategoria']) ? sanitize($input['subcategoria']) : null;
$data_inicial = $input['data'] ?? date('Y-m-d');
$repetir_meses = (int)($input['repetir_meses'] ?? 1); // Quantidade de meses para repetir o lançamento

// Validação de campos obrigatórios
if (empty($descricao) || $valor <= 0 || empty($categoria)) {
    sendError('Preencha os campos obrigatórios corretamente.');
}

// Limites de segurança para a recorrência (evita loops infinitos ou sobrecarga)
if ($repetir_meses < 1) $repetir_meses = 1;
if ($repetir_meses > 24) $repetir_meses = 24;

try {
    $userId = getCurrentUserId();
    
    // Inicia uma TRANSAÇÃO no banco de dados.
    // Isso garante que se houver erro ao criar uma despesa de um mês futuro,
    // nenhuma das outras será gravada, mantendo a integridade dos dados.
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO despesas (user_id, descricao, valor, categoria, subcategoria, data) VALUES (?, ?, ?, ?, ?, ?)");

    // Laço para criar as cópias das despesas nos meses seguintes
    for ($i = 0; $i < $repetir_meses; $i++) {
        // Calcula a nova data adicionando $i meses à data inicial
        $data_atual = date('Y-m-d', strtotime("+$i month", strtotime($data_inicial)));
        $stmt->execute([$userId, $descricao, $valor, $categoria, $subcategoria, $data_atual]);
    }

    // Se tudo deu certo, confirma as gravações no banco
    $pdo->commit();
    
    sendJson(['message' => $repetir_meses > 1 ? "Lançamentos criados para os próximos $repetir_meses meses!" : "Despesa adicionada com sucesso!"]);

} catch (PDOException $e) {
    // Se houve qualquer erro, desfaz tudo o que foi tentado no laço for
    if ($pdo->inTransaction()) $pdo->rollBack();
    sendError('Erro ao salvar despesa(s): ' . $e->getMessage());
}
