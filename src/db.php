<?php
/**
 * src/db.php - Conexão Centralizada com o Banco de Dados
 */

require_once __DIR__ . '/config.php';

// Montagem do DSN (Data Source Name) usando as constantes fixas de config.php
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // Se ocorrer um erro de conexão, para a execução e exibe a mensagem amigável
    die("❌ Erro de conexão com o banco de dados. Por favor, verifique suas configurações no arquivo src/config.php");
}
