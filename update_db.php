<?php
/**
 * update_db.php - Reparador Automático do Banco de Dados
 */
require_once 'src/db.php';

echo "<h1>Reparador de Banco de Dados 🛠️</h1>";

try {
    // 1. Adiciona a coluna subcategoria se ela não existir
    echo "Verificando coluna 'subcategoria' na tabela 'despesas'... ";
    try {
        $pdo->exec("ALTER TABLE despesas ADD COLUMN subcategoria VARCHAR(50) DEFAULT NULL AFTER categoria");
        echo "<b style='color: green;'>ADICIONADA!</b><br>";
    } catch (Exception $e) {
        echo "<b style='color: orange;'>Já existe ou ignorada.</b><br>";
    }

    // 2. Garante que a tabela de Metas existe
    echo "Verificando tabela 'metas'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS metas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        titulo VARCHAR(100) NOT NULL,
        valor_objetivo DECIMAL(10, 2) NOT NULL,
        valor_poupado DECIMAL(10, 2) DEFAULT 0.00,
        prazo DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "<b style='color: green;'>OK!</b><br>";

    // 3. Garante que a tabela de Orçamentos existe
    echo "Verificando tabela 'orcamentos'... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS orcamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        valor_limite DECIMAL(10, 2) NOT NULL,
        UNIQUE KEY user_cat (user_id, categoria),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "<b style='color: green;'>OK!</b><br>";

    echo "<hr><h2 style='color: blue;'>✅ Tudo pronto! Tente salvar a despesa agora.</h2>";
    echo "<p><a href='index.php'>Voltar para o Início</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erro ao reparar:</h2> " . $e->getMessage();
}
