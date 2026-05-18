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