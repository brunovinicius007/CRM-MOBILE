<?php
require_once 'src/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS metas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        titulo VARCHAR(100) NOT NULL,
        valor_objetivo DECIMAL(10, 2) NOT NULL,
        valor_poupado DECIMAL(10, 2) DEFAULT 0.00,
        prazo DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "Tabela de metas criada!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
