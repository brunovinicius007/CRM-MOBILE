<?php
/**
 * test_conexao.php - Utilitário para diagnosticar erro na hospedagem
 */
require_once 'src/utils.php';
loadEnv(__DIR__ . '/.env');

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

echo "<h1>Teste de Conexão 🔍</h1>";
echo "<b>Tentando conectar com:</b><br>";
echo "Host: $host<br>";
echo "Banco: $db<br>";
echo "Usuário: $user<br>";
echo "Senha: " . (empty($pass) ? "[Vazia]" : "******") . "<br><hr>";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "<h2 style='color: green;'>✅ SUCESSO! O PHP conseguiu se conectar ao banco de dados.</h2>";
    echo "<p>Se o sistema ainda der erro, verifique se você rodou o <b>setup.php</b> para criar as tabelas.</p>";
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ FALHA NA CONEXÃO</h2>";
    echo "<b>Erro retornado pelo servidor:</b> " . $e->getMessage();
    echo "<br><br><b>Dicas:</b><br>";
    echo "1. Na hospedagem, o Host geralmente é 'localhost', mas algumas empresas usam um endereço diferente.<br>";
    echo "2. Verifique se o nome do banco e o usuário têm o prefixo da sua conta (Ex: usuario_iafinance).<br>";
    echo "3. Verifique se o usuário tem todas as permissões no banco de dados.";
}
