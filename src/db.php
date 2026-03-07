<?php
/**
 * src/db.php - Conexão com o Banco de Dados
 */

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/config.php';

// Carrega as variáveis do arquivo .env se ele existir (diretório raiz)
loadEnv(__DIR__ . '/../.env');

// Pega os dados com prioridade para o .env, senão usa config.php (defines)
$host    = getenv('DB_HOST') ?: (defined('DB_HOST') ? DB_HOST : 'localhost');
$db      = getenv('DB_NAME') ?: (defined('DB_NAME') ? DB_NAME : '');
$user    = getenv('DB_USER') ?: (defined('DB_USER') ? DB_USER : '');
$pass    = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (defined('DB_PASS') ? DB_PASS : '');
$charset = getenv('DB_CHARSET') ?: (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";


$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
