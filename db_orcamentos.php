<?php
require_once 'src/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS orcamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        valor_limite DECIMAL(10, 2) NOT NULL,
        UNIQUE KEY user_cat (user_id, categoria),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "Tabela de orçamentos criada!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
