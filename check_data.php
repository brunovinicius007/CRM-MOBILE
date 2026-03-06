<?php
require_once 'src/db.php';
echo "--- RECEITAS ---\n";
$stmt = $pdo->query("SELECT data, valor FROM receitas");
while($row = $stmt->fetch()) {
    echo "Data: " . $row['data'] . " | Valor: " . $row['valor'] . "\n";
}
echo "--- DESPESAS ---\n";
$stmt = $pdo->query("SELECT data, valor FROM despesas");
while($row = $stmt->fetch()) {
    echo "Data: " . $row['data'] . " | Valor: " . $row['valor'] . "\n";
}
