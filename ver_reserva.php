<?php
include "restaurante.php";

$codigo = $_GET['codigo_reserva'] ?? '';

if (!$codigo) {
    die("Código não enviado.");
}

$stmt = $conn->prepare("SELECT * FROM reservas WHERE codigo_reserva = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Reserva não encontrada.");
}

$reserva = $result->fetch_assoc();

echo "
<div style='padding:20px; background:#111; color:white; border-radius:15px; max-width:400px; margin:auto;'>
    <h2>Minha Reserva</h2>

    <p><b>Código:</b> {$reserva['codigo_reserva']}</p>
    <p><b>Nome:</b> {$reserva['nome']}</p>
    <p><b>Data:</b> {$reserva['data_reserva']}</p>
    <p><b>Hora:</b> {$reserva['hora_reserva']}</p>
    <p><b>Mesa:</b> {$reserva['mesa']}</p>
    <p><b>Estado:</b> {$reserva['estado']}</p>

    <br>
    
    <a href='cancelar_reserva.php?codigo={$reserva['codigo_reserva']}' style='color:red; display:flex'>Cancelar Reserva</a> <a href='formulario.html'style='color:white;text-decoration:underline;'>Voltar</a>
</div>
";
?>