<?php
session_start();

/* =====================================
   PROTEGER ÁREA ADMIN
===================================== */

if (!isset($_SESSION['admin_id'])) {
    header("Location: gerente_login.html");
    exit();
}

/* =====================================
   CONEXÃO
===================================== */

$conn = new mysqli("localhost", "root", "", "restaurante");

if ($conn->connect_error) {
    die("Erro de conexão.");
}

$conn->set_charset("utf8mb4");

/* =====================================
   ADICIONAR PRATO
===================================== */

if(isset($_POST['add_prato'])){

    $nome        = trim($_POST['nome']);
    $categoria   = trim($_POST['categoria']);
    $descricao   = trim($_POST['descricao']);
    $preco       = floatval($_POST['preco']);
    $quantidade  = intval($_POST['quantidade']);

    $imagem = "";

    /* CRIAR PASTA */

    if(!file_exists("uploads")){
        mkdir("uploads", 0777, true);
    }

    /* UPLOAD */

    if(!empty($_FILES['imagem']['name'])){

        $permitidas = ['jpg','jpeg','png','webp'];

        $ext = strtolower(
            pathinfo(
                $_FILES['imagem']['name'],
                PATHINFO_EXTENSION
            )
        );

        if(!in_array($ext, $permitidas)){
            die("Imagem inválida.");
        }

        $imagem = uniqid() . "." . $ext;

        move_uploaded_file(
            $_FILES['imagem']['tmp_name'],
            "uploads/" . $imagem
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO pratos
        (nome, categoria, descricao, preco, qtdprato, imagem)

        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssdis",
        $nome,
        $categoria,
        $descricao,
        $preco,
        $quantidade,
        $imagem
    );

    $stmt->execute();

    header("Location: pratos.php");
    exit();
}

/* =====================================
   EXCLUIR
===================================== */

if(isset($_GET['excluir'])){

    $id = intval($_GET['excluir']);

    $stmt = $conn->prepare("
        DELETE FROM pratos
        WHERE id=?
    ");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    header("Location: pratos.php");
    exit();
}

/* =====================================
   LISTAR PRATOS
===================================== */

$pratos = $conn->query("
    SELECT *
    FROM pratos
    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html lang="pt">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Pratos</title>

<link rel="stylesheet" href="dashboard.css">

<style>

/* =====================================
   MAIN
===================================== */

.main{
    padding:25px;
}

/* =====================================
   TOPO
===================================== */

.topbar{
    margin-bottom:25px;
}

.topbar h1{
    color:#111;
    margin-bottom:5px;
}

.subtitulo{
    color:#666;
    font-size:14px;
}

/* =====================================
   FORMULÁRIO
===================================== */

.form-box{
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.grid-form{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:15px;
    margin-bottom:15px;
}

.form-box input,
.form-box textarea{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
    outline:none;
    font-size:14px;
}

.form-box input:focus,
.form-box textarea:focus{
    border-color:#d62828;
}

.form-box textarea{
    resize:none;
    min-height:100px;
    margin-bottom:15px;
}

.form-box button{
    background:#d62828;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
    font-weight:bold;
}

.form-box button:hover{
    background:#b71c1c;
}

/* =====================================
   TABELA
===================================== */

.table-box{
    width:100%;
    overflow-x:auto;
    background:white;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:1000px;
}

thead{
    background:#111;
    color:white;
}

th{
    padding:15px;
    text-align:left;
    font-size:14px;
}

td{
    padding:15px;
    border-bottom:1px solid #eee;
    font-size:14px;
    vertical-align:middle;
}

tr:hover{
    background:#fafafa;
}

/* =====================================
   IMAGEM
===================================== */

td img{
    width:70px;
    height:70px;
    border-radius:8px;
    object-fit:cover;
}

/* =====================================
   PREÇO
===================================== */

.preco{
    color:#16a34a;
    font-weight:bold;
}

/* =====================================
   STOCK
===================================== */

.stock{
    font-weight:bold;
}

.baixo-stock{
    color:#dc2626;
}

/* =====================================
   BOTÕES
===================================== */

.btn{
    display:inline-block;
    padding:8px 14px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    color:white;
}

.excluir{
    background:#dc2626;
}

.excluir:hover{
    background:#b91c1c;
}

/* =====================================
   RESPONSIVO
===================================== */

@media(max-width:768px){

    .main{
        padding:15px;
    }

    .table-box{
        overflow-x:auto;
    }

    table{
        min-width:900px;
    }

}

</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">

    <!-- TOPO -->
    <div class="topbar">

        <h1>Gestão de Pratos</h1>

        <span class="subtitulo">
            Administração do cardápio do restaurante
        </span>

    </div>

    <!-- FORM -->
    <div class="form-box">

        <form method="POST"
        enctype="multipart/form-data">

            <input type="hidden"
            name="add_prato"
            value="1">

            <div class="grid-form">

                <input type="text"
                name="nome"
                placeholder="Nome do prato"
                required>

                <input type="text"
                name="categoria"
                placeholder="Categoria"
                required>

                <input type="number"
                step="0.01"
                name="preco"
                placeholder="Preço"
                required>

                <input type="number"
                name="quantidade"
                placeholder="Quantidade"
                required>

            </div>

            <textarea
            name="descricao"
            placeholder="Descrição"
            required></textarea>

            <input type="file"
            name="imagem"
            required>

            <br><br>

            <button type="submit">
                Adicionar Prato
            </button>

        </form>

    </div>

    <!-- TABELA -->
    <div class="table-box">

        <table>

            <thead>

                <tr>

                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Ações</th>

                </tr>

            </thead>

            <tbody>

            <?php while($p = $pratos->fetch_assoc()) { ?>

                <?php

                $classe_stock = "";

                if($p['qtdprato'] <= 5){
                    $classe_stock = "baixo-stock";
                }

                ?>

                <tr>

                    <td>

                        <img src="uploads/<?= htmlspecialchars($p['imagem']) ?>">

                    </td>

                    <td>
                        <?= htmlspecialchars($p['nome']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($p['categoria']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($p['descricao']) ?>
                    </td>

                    <td class="preco">
                        <?= number_format($p['preco'], 2, ',', '.') ?> Kz
                    </td>

                    <td class="stock <?= $classe_stock ?>">

                        <?= $p['qtdprato'] ?>

                    </td>

                    <td>

                        <a href="?excluir=<?= $p['id'] ?>"
                        class="btn excluir"
                        onclick="return confirm('Excluir prato?')">

                            Excluir

                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>