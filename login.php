<?php
session_start();

include("conexao.php");
include("restaurante.php");

/* =========================
   RECEBER DADOS
========================= */

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

/* =========================
   VALIDAR CAMPOS
========================= */

if (empty($nome) || empty($email) || empty($senha)) {

    echo "<script>
            alert('Preencha todos os campos!');
            window.location.href='gerente_login.html';
          </script>";
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

        echo "<script>
                alert('Senha incorreta!');
                window.location.href='gerente_login.html';
              </script>";
    }

} else {

    echo "<script>
            alert('Administrador não encontrado!');
            window.location.href='gerente_login.html';
          </script>";
}

$stmt->close();
$conn->close();
?>