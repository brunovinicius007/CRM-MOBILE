<?php
// api/list_usuarios.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

try {
    if (isAdmin()) {
        // Admin vê todo mundo
        $stmt = $pdo->query("SELECT id, nome, email, role, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
    } else {
        // Usuário comum só vê a si mesmo
        $stmt = $pdo->prepare("SELECT id, nome, email, role, created_at FROM users WHERE id = ?");
        $stmt->execute([getCurrentUserId()]);
        $users = $stmt->fetchAll();
    }
    sendJson($users);
} catch (PDOException $e) {
    sendError('Erro ao buscar dados do usuário.');
}
