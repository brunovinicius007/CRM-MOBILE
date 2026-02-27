<?php
// api/register.php
require_once '../src/db.php';
require_once '../src/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$nome = $input['nome'] ?? '';
$email = $input['email'] ?? '';
$senha = $input['senha'] ?? '';

if (empty($nome) || empty($email) || empty($senha)) {
    sendError('Preencha todos os campos.');
}

// Check if email exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    sendError('E-mail já cadastrado.');
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $senha_hash]);
    sendJson(['message' => 'Usuário cadastrado com sucesso!']);
} catch (PDOException $e) {
    sendError('Erro ao cadastrar usuário.');
}
