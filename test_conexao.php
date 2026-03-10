<?php
/**
 * test_conexao.php - Script de teste rápido
 */

require_once 'src/config.php';

echo "<h3>Testando conexão com as constantes do config.php...</h3>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    echo "✅ Conexão realizada com sucesso!<br>";
    echo "Host: " . DB_HOST . "<br>";
    echo "Banco: " . DB_NAME . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Erro ao conectar: " . $e->getMessage();
}
