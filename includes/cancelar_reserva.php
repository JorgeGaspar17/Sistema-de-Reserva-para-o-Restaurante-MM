<?php
include "restaurante.php";

$codigo = $_GET['codigo'] ?? '';

$stmt = $conn->prepare("UPDATE reservas SET estado='Cancelada' WHERE codigo_reserva=?");
$stmt->bind_param("s", $codigo);
$stmt->execute();

echo "Reserva cancelada com sucesso. <a href='formulario.html'>Voltar</a>";
?>