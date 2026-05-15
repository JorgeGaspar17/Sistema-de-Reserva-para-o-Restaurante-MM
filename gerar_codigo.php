<?php
$codigo = $_GET['codigo'] ?? 'SEM-CODIGO';

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=reserva.txt");

echo "===== RESERVA =====\n";
echo "Código: $codigo\n";
echo "Guarde este código para verificar sua reserva.\n";
?>