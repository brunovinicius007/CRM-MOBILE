<?php
// api/login.php
error_reporting(0); // Desativa avisos que quebram o JSON
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$email = strtolower(trim($input['email'] ?? ''));
$senha = $input['senha'] ?? '';

if (empty($email) || empty($senha)) {
    sendError('Preencha e-mail e senha.');
}

try {
    // Busca o usuário ignorando maiúsculas/minúsculas no e-mail
    $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha_hash'])) {
        loginUser($user);
        sendJson(['message' => 'Login realizado com sucesso!', 'redirect' => 'dashboard.php']);
    } else {
        sendError('E-mail ou senha inválidos.', 401);
    }
} catch (Exception $e) {
    sendError('Erro interno no servidor.');
}
