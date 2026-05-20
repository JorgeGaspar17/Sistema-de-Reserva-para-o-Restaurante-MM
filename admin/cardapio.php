<?php
require_once __DIR__ . '/../includes/admin_init.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio</title>
    <link rel="stylesheet" href="../assets/css/cardapio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <h1>Cardápio do Restaurante</h1>
        <span class="subtitulo">Visualize os pratos disponíveis no sistema</span>
    </div>

    <section class="menu-category">
        <div id="lista-pratos"></div>
    </section>
</div>

<script>
    fetch('../includes/listar_pratos.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('lista-pratos').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('lista-pratos').innerHTML = '<p>Erro ao carregar os pratos.</p>';
            console.error(error);
        });
</script>

</body>
</html>