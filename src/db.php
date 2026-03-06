<?php
/**
 * src/db.php - Configuração de Conexão com o Banco de Dados
 */

require_once 'utils.php';

// Carrega as variáveis do arquivo .env localizado na raiz
loadEnv(__DIR__ . '/../.env');

// Busca as configurações (tenta getenv, se falhar tenta $_ENV)
$host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$db   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'iafinance_crm');
$user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
$charset = getenv('DB_CHARSET') ?: ($_ENV['DB_CHARSET'] ?? 'utf8mb4');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Mostra o erro real apenas se necessário para debug, remova em produção definitiva
    die("Erro de conexão: " . $e->getMessage());
}
