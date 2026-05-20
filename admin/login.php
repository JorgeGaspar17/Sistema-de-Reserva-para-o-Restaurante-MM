<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/gerente_login.php');
    exit();
}

require_once __DIR__ . '/../config/conexao.php';

/* =========================
   RECEBER DADOS
========================= */

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$csrf  = $_POST['csrf_token'] ?? '';

/* =========================
   VALIDAR CAMPOS
========================= */

if (empty($nome) || empty($email) || empty($senha)) {
    header('Location: ../pages/gerente_login.php?error=empty');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/gerente_login.php?error=invalid_email');
    exit();
}

if (empty($csrf) || !hash_equals($_SESSION['token'] ?? '', $csrf)) {
    header('Location: ../pages/gerente_login.php?error=csrf');
    exit();
}

/* =========================
   BUSCAR ADMIN
========================= */

$sql = "SELECT * FROM adimin 
        WHERE nome = ? 
        AND email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param("ss", $nome, $email);

$stmt->execute();

$resultado = $stmt->get_result();

/* =========================
   VERIFICAR USUÁRIO
========================= */

if ($resultado->num_rows > 0) {

    $admin = $resultado->fetch_assoc();

    /* =========================
       VERIFICAR SENHA HASH
    ========================= */

    if (password_verify($senha, $admin['senha'])) {

        /* SEGURANÇA DE SESSÃO */
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];

        header("Location: gerente_dashboard.php");
        exit();

    } else {
        header('Location: ../pages/gerente_login.php?error=credentials');
        exit();
    }

} else {
    header('Location: ../pages/gerente_login.php?error=credentials');
    exit();
}

$stmt->close();
$conn->close();
?>