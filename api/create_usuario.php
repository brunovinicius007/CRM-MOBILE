<?php
// api/create_usuario.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

// Apenas administradores podem criar usuários aqui
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$nome = $input['nome'] ?? '';
$email = $input['email'] ?? '';
$senha = $input['senha'] ?? '';
$role = $input['role'] ?? 'user';

if (empty($nome) || empty($email) || empty($senha)) {
    sendError('Preencha todos os campos obrigatórios.');
}

// Validar e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('E-mail inválido.');
}

// Validar role
if (!in_array($role, ['admin', 'user'])) {
    $role = 'user';
}

// Verificar se e-mail já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    sendError('E-mail já cadastrado.');
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha_hash, $role]);
    sendJson(['message' => 'Usuário criado com sucesso!']);
} catch (PDOException $e) {
    sendError('Erro ao criar usuário.');
}
