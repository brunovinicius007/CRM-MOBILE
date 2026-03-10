<?php
/**
 * setup.php - Instalador Automático (Configurado para config.php)
 */
require_once 'src/config.php';

try {
    echo "<h1>Minhas Finanças Setup 🛠️</h1>";
    
    echo "<p>Conectando ao banco <b>" . DB_NAME . "</b> no host <b>" . DB_HOST . "</b>...</p>";
    
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<p>✅ Conectado ao banco de dados!</p>";
    
    // SQL para criar as tabelas
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        senha_hash VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS receitas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        valor DECIMAL(10, 2) NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS despesas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        valor DECIMAL(10, 2) NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        subcategoria VARCHAR(50) DEFAULT NULL,
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS metas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        titulo VARCHAR(100) NOT NULL,
        valor_objetivo DECIMAL(10, 2) NOT NULL,
        valor_poupado DECIMAL(10, 2) DEFAULT 0.00,
        prazo DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS orcamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        valor_limite DECIMAL(10, 2) NOT NULL,
        UNIQUE KEY user_cat (user_id, categoria),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";

    $pdo->exec($sql);
    echo "<p>✅ Tabelas criadas/verificadas com sucesso.</p>";

    // Criar usuário de teste
    $nome = "Usuário Teste";
    $email = "admin@teste.com";
    $senha = "123456";
    $role = "admin";
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->fetch()) {
        echo "<p>⚠️ O usuário <b>$email</b> já existe.</p>";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senhaHash, $role]);
        echo "<p>✅ Usuário de teste criado com sucesso!</p>";
    }

    echo "<hr>";
    echo "<p><a href='index.php' style='padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px;'>➡️ Ir para Login</a></p>";

} catch (PDOException $e) {
    echo "<h1>❌ Erro Fatal</h1>";
    echo "<p>Detalhe: " . $e->getMessage() . "</p>";
}
