<?php
include "restaurante.php";

$codigo = trim($_GET['codigo_reserva'] ?? '');
$email = trim($_GET['email'] ?? '');
$telefone = trim($_GET['telefone'] ?? '');

if ($codigo === '' && $email === '' && $telefone === '') {
    die("Forneça o código, e-mail ou telefone para localizar a reserva.");
}

if ($codigo !== '') {
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE codigo_reserva = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif ($email !== '') {
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE email = ? ORDER BY id DESC");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // buscar por telefone (normalizar dígitos)
    $tel_digits = preg_replace('/\D+/', '', $telefone);
    $like = "%" . $tel_digits . "%";
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE REPLACE(REPLACE(REPLACE(telefone,' ',''),'-',''),'.','') LIKE ? ORDER BY id DESC");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
}

if (!$result || $result->num_rows == 0) {
    die("Reserva não encontrada.");
}

if ($result->num_rows == 1) {
    $reserva = $result->fetch_assoc();
    echo "<div style='padding:20px; background:#111; color:white; border-radius:15px; max-width:520px; margin:auto; font-family:Arial;'>";
    echo "<h2>Minha Reserva</h2>";
    echo "<p><b>Código:</b> " . htmlspecialchars($reserva['codigo_reserva']) . "</p>";
    echo "<p><b>Nome:</b> " . htmlspecialchars($reserva['nome']) . "</p>";
    echo "<p><b>Data:</b> " . htmlspecialchars($reserva['data_reserva']) . "</p>";
    echo "<p><b>Hora:</b> " . htmlspecialchars($reserva['hora_reserva']) . "</p>";
    echo "<p><b>Mesa:</b> " . htmlspecialchars($reserva['mesa']) . "</p>";
    echo "<p><b>Estado:</b> " . htmlspecialchars($reserva['estado']) . "</p>";
    echo "<br>";
    echo "<a href='cancelar_reserva.php?codigo=" . urlencode($reserva['codigo_reserva']) . "' style='color:red; display:block; margin-bottom:8px;'>Cancelar Reserva</a>";
    echo "<a href='../pages/formulario.php' style='color:white;text-decoration:underline;'>Voltar</a>";
    echo "</div>";
} else {
    // múltiplas reservas: listar
    echo "<div style='max-width:760px;margin:30px auto;font-family:Arial;'>";
    echo "<h2>Reservas encontradas</h2>";
    while ($reserva = $result->fetch_assoc()) {
        echo "<div style='padding:12px;border-radius:10px;background:#fff;border:1px solid #eee;margin-bottom:12px;'>";
        echo "<p><strong>Código:</strong> " . htmlspecialchars($reserva['codigo_reserva']) . "</p>";
        echo "<p><strong>Nome:</strong> " . htmlspecialchars($reserva['nome']) . "</p>";
        echo "<p><strong>Data:</strong> " . htmlspecialchars($reserva['data_reserva']) . "</p>";
        echo "<p><strong>Hora:</strong> " . htmlspecialchars($reserva['hora_reserva']) . "</p>";
        echo "<p><strong>Mesa:</strong> " . htmlspecialchars($reserva['mesa']) . "</p>";
        echo "<p><strong>Estado:</strong> " . htmlspecialchars($reserva['estado']) . "</p>";
        echo "<p><a href='cancelar_reserva.php?codigo=" . urlencode($reserva['codigo_reserva']) . "' style='color:red;'>Cancelar</a> <a href='../pages/formulario.php' style='margin-left:8px;text-decoration:underline;'>Voltar</a></p>";
        echo "</div>";
    }
    echo "</div>";
}
?>