<?php
require_once __DIR__ . '/../includes/admin_init.php';

$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT * FROM pratos
    WHERE id=?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$prato = $result->fetch_assoc();

if(isset($_POST['editar'])){
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        header('Location: pratos.php');
        exit();
    }

    $nome       = trim($_POST['nome'] ?? '');
    $categoria  = trim($_POST['categoria'] ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $preco      = floatval($_POST['preco'] ?? 0);
    $quantidade = intval($_POST['quantidade'] ?? 0);

    $stmt = $conn->prepare("
        UPDATE pratos
        SET
        nome=?,
        categoria=?,
        descricao=?,
        preco=?,
        qtdprato=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sssdii",
        $nome,
        $categoria,
        $descricao,
        $preco,
        $quantidade,
        $id
    );

    $stmt->execute();

    header("Location: pratos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Editar Prato</title>

<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

.form-editar{
    max-width:700px;
    margin:auto;
    background:#fff;
    padding:30px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.form-editar h1{
    margin-bottom:20px;
}

.form-editar input,
.form-editar textarea{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border-radius:10px;
    border:1px solid #ccc;
}

.form-editar button{
    background:red;
    color:white;
    border:none;
    padding:14px 25px;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
}

</style>

</head>
<body>

<div class="form-editar">

<h1>Editar Prato</h1>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <input type="text"
    name="nome"
    value="<?= htmlspecialchars($prato['nome']) ?>"
    required>

<input type="text"
name="categoria"
value="<?= htmlspecialchars($prato['categoria']) ?>"
required>

<input type="number"
step="0.01"
name="preco"
value="<?= $prato['preco'] ?>"
required>

<input type="number"
name="quantidade"
value="<?= $prato['qtdprato'] ?>"
required>

<textarea
name="descricao"
required><?= htmlspecialchars($prato['descricao']) ?></textarea>

<button type="submit" name="editar">
Salvar Alterações
</button>

</form>

</div>

</body>
</html>