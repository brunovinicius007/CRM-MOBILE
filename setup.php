<?php
require_once 'src/db.php';

try {
    echo "<h1>IAFinance Setup 🛠️</h1>";
    
    // 1. Connection Check
    echo "<p>✅ Conexão com banco de dados em <b>src/db.php</b> bem sucedida!</p>";

    // 2. Create Tables (Reading from database.sql or defining here)
    // We'll define here to be safe and independent of file paths
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        senha_hash VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS receitas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        valor DECIMAL(10, 2) NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS despesas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        valor DECIMAL(10, 2) NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ";

    $pdo->exec($sql);
    echo "<p>✅ Tabelas criadas/verificadas com sucesso.</p>";

    // 3. Create Test User
    $nome = "Usuário Teste";
    $email = "admin@teste.com";
    $senha = "123456";
    $role = "admin";
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->fetch()) {
        echo "<p>⚠️ O usuário <b>$email</b> já existe. Senha inalterada.</p>";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senhaHash, $role]);
        echo "<p>✅ Usuário de teste criado com sucesso!</p>";
    }

    echo "<hr>";
    echo "<h3>Dados de Acesso:</h3>";
    echo "<ul>";
    echo "<li><b>Email:</b> $email</li>";
    echo "<li><b>Senha:</b> $senha</li>";
    echo "</ul>";
    echo "<p><a href='index.php'>➡️ Ir para Login</a></p>";

} catch (PDOException $e) {
    echo "<h1>❌ Erro Fatal</h1>";
    echo "<p>Não foi possível conectar ou configurar o banco.</p>";
    echo "<p><b>Detalhe do Erro:</b> " . $e->getMessage() . "</p>";
    echo "<hr>";
    echo "<h3>Como corrigir:</h3>";
    echo "<ol>";
    echo "<li>Abra o arquivo <code>src/db.php</code></li>";
    echo "<li>Verifique se o HOST, NOME DO BANCO, USUÁRIO e SENHA estão corretos conforme seu painel de hospedagem.</li>";
    echo "<li>O erro acima geralmente indica senha errada ou usuário sem permissão no banco.</li>";
    echo "</ol>";
}
