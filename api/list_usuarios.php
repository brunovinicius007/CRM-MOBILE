<?php
// api/list_usuarios.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAdmin();

try {
    $stmt = $pdo->query("SELECT id, nome, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    sendJson($users);
} catch (PDOException $e) {
    sendError('Erro ao buscar usuários.');
}
