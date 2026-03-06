<?php
require_once 'src/db.php';
try {
    $pdo->exec("ALTER TABLE despesas ADD COLUMN subcategoria VARCHAR(50) DEFAULT NULL AFTER categoria");
    echo "Sucesso!";
} catch (Exception $e) {
    echo "A coluna já existe ou erro: " . $e->getMessage();
}
