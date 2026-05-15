<!DOCTYPE html>
<html lang="pt-pt">

<head>
<meta charset="UTF-8">
<title>Cardápio</title>
<link rel="stylesheet" href="cardapio.css">
</head>

<body>

<h1>Cardápio do Restaurante</h1>

<div id="lista-pratos"></div>

<script>
fetch('listar_pratos.php')
.then(response => response.text())
.then(data => {
    document.getElementById("lista-pratos").innerHTML = data;
});
</script>

</body>
</html>