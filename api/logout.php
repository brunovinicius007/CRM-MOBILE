<?php
// api/logout.php
require_once '../src/auth.php';
require_once '../src/utils.php';

logoutUser();
sendJson(['message' => 'Logout realizado com sucesso!', 'redirect' => 'index.php']);
