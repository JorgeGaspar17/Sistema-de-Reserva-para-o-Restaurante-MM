<?php
$conn = new mysqli("localhost", "root", "", "restaurante");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
$sql = "SELECT * FROM pratos ORDER BY id DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {?>
        
     <div class="menu-item">

    <img src="uploads/<?php echo $row['imagem']; ?>" class="item-image">

    <div class="item-details">
        <h3><?php echo $row['nome']; ?></h3>

        <p><?php echo $row['descricao']; ?></p>

    </div>

    <span class="item-price">
        <?php echo number_format($row['preco'], 2, ',', '.'); ?> Kz
    </span>

</div>
        <?php
    }
} else {
    echo "<p>Nenhum prato cadastrado ainda.</p>";
}
?>