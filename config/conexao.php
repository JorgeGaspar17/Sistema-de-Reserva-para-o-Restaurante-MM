<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "restaurante";

$conn = new mysqli($servidor, $usuario, $senha, $banco);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

?>