<?php
// api/process_ofx.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['ofx_file'])) {
    sendError('Nenhum arquivo enviado.', 400);
}

$file = $_FILES['ofx_file']['tmp_name'];
$content = file_get_contents($file);

if (!$content) {
    sendError('Erro ao ler o arquivo OFX.');
}

$transactions = [];
// Processador simples de formato OFX (usando Regex para extrair blocos de transação)
if (preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/s', $content, $matches)) {
    foreach ($matches[1] as $txn) {
        preg_match('/<TRNAMT>(.*?)(?:<|\r|\n)/', $txn, $amtMatch);
        preg_match('/<DTPOSTED>([0-9]{8})/', $txn, $dateMatch); // Formato YYYYMMDD
        preg_match('/<MEMO>(.*?)(?:<|\r|\n)/', $txn, $memoMatch);
        
        if (!$memoMatch) {
             preg_match('/<NAME>(.*?)(?:<|\r|\n)/', $txn, $memoMatch);
        }

        if ($amtMatch && $dateMatch) {
            $amt = (float)$amtMatch[1];
            $dateStr = $dateMatch[1];
            $date = substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
            $memo = $memoMatch ? trim($memoMatch[1]) : 'Transação Importada';
            
            $transactions[] = [
                'tipo' => $amt > 0 ? 'receita' : 'despesa',
                'valor' => abs($amt),
                'data' => $date,
                'descricao' => substr($memo, 0, 100)
            ];
        }
    }
}

if (count($transactions) === 0) {
    sendError('Nenhuma transação válida encontrada no arquivo. Verifique se é um arquivo OFX válido de extrato.');
}

$userId = getCurrentUserId();
$inserted = 0;

$pdo->beginTransaction();
try {
    $stmtDespesa = $pdo->prepare("INSERT INTO despesas (user_id, descricao, valor, categoria, data) VALUES (?, ?, ?, 'Outros', ?)");
    $stmtReceita = $pdo->prepare("INSERT INTO receitas (user_id, descricao, valor, categoria, data) VALUES (?, ?, ?, 'Outros', ?)");

    foreach ($transactions as $t) {
        if ($t['tipo'] === 'despesa') {
            $stmtDespesa->execute([$userId, $t['descricao'], $t['valor'], $t['data']]);
        } else {
            $stmtReceita->execute([$userId, $t['descricao'], $t['valor'], $t['data']]);
        }
        $inserted++;
    }
    
    $pdo->commit();
    sendJson(['message' => "$inserted transações foram importadas com sucesso para a categoria 'Outros'!"]);
} catch (Exception $e) {
    $pdo->rollBack();
    sendError('Erro ao salvar as transações importadas.');
}
