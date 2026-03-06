<?php
/**
 * src/auth.php - Motor de Autenticação e Segurança
 * Gerencia sessões, permissões de usuário e controle de acesso às páginas.
 */

// Inicia a sessão se ainda não tiver sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verifica se o usuário logado tem nível de Administrador
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Protege páginas que exigem login.
 * Se for uma chamada de API (/api/), retorna JSON 401.
 * Se for uma página comum, redireciona para o Login.
 */
function requireAuth() {
    if (!isLoggedIn()) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Sessão expirada. Faça login novamente.']);
            exit;
        }
        header('Location: index.php');
        exit;
    }
}

/**
 * Protege páginas exclusivas para Administradores.
 */
function requireAdmin() {
    requireAuth(); // Primeiro precisa estar logado
    if (!isAdmin()) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado. Apenas administradores podem realizar esta ação.']);
            exit;
        }
        http_response_code(403);
        echo "Acesso negado.";
        exit;
    }
}

/**
 * Retorna o ID do usuário atualmente logado
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Salva os dados do usuário na sessão após o login bem-sucedido
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['nome'] = $user['nome'];
    $_SESSION['role'] = $user['role'];
}

/**
 * Encerra a sessão e desloga o usuário
 */
function logoutUser() {
    session_unset();
    session_destroy();
}
