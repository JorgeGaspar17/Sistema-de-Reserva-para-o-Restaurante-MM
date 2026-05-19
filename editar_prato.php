<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: gerente_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "restaurante");

$conn->set_charset("utf8mb4");

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

    $nome       = $_POST['nome'];
    $categoria  = $_POST['categoria'];
    $descricao  = $_POST['descricao'];
    $preco      = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

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
        "sssdis",
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