<?php
$conn = new mysqli("localhost", "root", "", "restaurante");

if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

?>
