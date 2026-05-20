<?php
session_start();

require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../pages/gerente_login.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verify_csrf($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitize($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function active_nav($page)
{
    return basename($_SERVER['PHP_SELF']) === basename($page) ? 'active' : '';
}
