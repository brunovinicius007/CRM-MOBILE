<?php
// api/update_usuario.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method Not Allowed', 405);
}

$input = getJsonInput();
$id = $input['id'] ?? null;
$nome = sanitize($input['nome'] ?? '');
$email = sanitize($input['email'] ?? '');
$role = sanitize($input['role'] ?? 'user');
$senha = $input['senha'] ?? ''; // Opcional (só muda se preencher)

if (!$id || empty($nome) || empty($email)) {
    sendError('Preencha os campos obrigatórios.');
}

// REGRA DE SEGURANÇA:
// Se NÃO for admin e tentar editar um ID diferente do seu, bloqueia!
if (!isAdmin() && $id != getCurrentUserId()) {
    sendError('Acesso negado. Você só pode editar seu próprio perfil.', 403);
}

// REGRA DE SEGURANÇA:
// Se NÃO for admin, ele não pode mudar o seu próprio 'role' (nível de acesso)
if (!isAdmin()) {
    $role = $_SESSION['role']; // Mantém o que já está na sessão
}

try {
    // 1. Atualiza dados básicos
    $sql = "UPDATE users SET nome = ?, email = ?, role = ? WHERE id = ?";
    $params = [$nome, $email, $role, $id];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // 2. Se digitou uma nova senha, atualiza a senha separadamente
    if (!empty($senha)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmtS = $pdo->prepare("UPDATE users SET senha_hash = ? WHERE id = ?");
        $stmtS->execute([$hash, $id]);
    }

    // Se o usuário editou a si mesmo, atualiza a sessão para refletir as mudanças na hora
    if ($id == getCurrentUserId()) {
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
    }

    sendJson(['message' => 'Usuário atualizado com sucesso!']);

} catch (PDOException $e) {
    sendError('Erro ao atualizar usuário. O e-mail pode já estar em uso.');
}
