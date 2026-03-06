<?php
// api/register.php
require_once '../src/db.php';
require_once '../src/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$nome = sanitize($input['nome'] ?? '');
$email = sanitize($input['email'] ?? '');
$senha = $input['senha'] ?? '';
$codigo = $input['codigo'] ?? '';

// VALIDAÇÃO DO CÓDIGO DE CONVITE
if (strtolower($codigo) !== 'nero') {
    sendError('Código de validação inválido. Você precisa de um convite para entrar.');
}

if (empty($nome) || empty($email) || empty($senha)) {
    sendError('Preencha todos os campos.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('E-mail inválido.');
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
