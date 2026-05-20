<?php
include ("../s_reserva/restaurante.php");
// garantir sessão para token CSRF
if (session_status() === PHP_SESSION_NONE) session_start();

// honeypot anti-bot
$hp = $_POST['hp'] ?? '';
if (!empty($hp)) {
    // provavel bot
    http_response_code(400);
    die('Requisição inválida.');
}

// verificar token CSRF
$posted_token = $_POST['csrf_token'] ?? '';
if (empty($posted_token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $posted_token)) {
    echo "<div style='max-width:600px;margin:40px auto;padding:20px;border-radius:12px;background:#ffcdd2;color:#560000;font-family:Arial;'>";
    echo "<h3>Erro de segurança: token inválido.</h3>";
    echo "<p><a href='../pages/formulario.php' style='color:#000;text-decoration:underline;'>Voltar ao formulário</a></p>";
    echo "</div>";
    exit();
}

/* =========================
   RECEBER DADOS
========================= */
$carrinho = $_POST['carrinho'] ?? '[]';
$carrinho = json_decode($carrinho, true);
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$data_reserva = $_POST['data_reserva'] ?? '';
$hora_reserva = $_POST['hora_reserva'] ?? '';
$num_pessoa = $_POST['num_pessoa'] ?? '';
$mesa = $_POST['mesa'] ?? '';
//$token = $_POST['token'] ?? '';

/* =========================
   VALIDAÇÃO SERVER-SIDE (EMAIL OU TELEFONE)
========================= */

$nome = trim($nome);
$email = trim($email);
$telefone = trim($telefone);

// Normalizar telefone para apenas dígitos para validação/armazenamento
$telefone_digits = preg_replace('/\D+/', '', $telefone);

$hasEmail = $email !== '';
$hasPhone = $telefone_digits !== '';

if (!$hasEmail && !$hasPhone) {
    echo "<div style='max-width:600px;margin:40px auto;padding:20px;border-radius:12px;background:#ffcdd2;color:#560000;font-family:Arial;'>";
    echo "<h3>Erro: Informe um e-mail ou telefone para contato.</h3>";
    echo "<p><a href='../pages/formulario.php' style='color:#000;text-decoration:underline;'>Voltar ao formulário</a></p>";
    echo "</div>";
    exit();
}

if ($hasEmail && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<div style='max-width:600px;margin:40px auto;padding:20px;border-radius:12px;background:#ffcdd2;color:#560000;font-family:Arial;'>";
    echo "<h3>Erro: E-mail inválido.</h3>";
    echo "<p><a href='../pages/formulario.php' style='color:#000;text-decoration:underline;'>Voltar ao formulário</a></p>";
    echo "</div>";
    exit();
}

if ($hasPhone && strlen($telefone_digits) < 9) {
    echo "<div style='max-width:600px;margin:40px auto;padding:20px;border-radius:12px;background:#ffcdd2;color:#560000;font-family:Arial;'>";
    echo "<h3>Erro: Telefone inválido. Informe pelo menos 9 dígitos.</h3>";
    echo "<p><a href='../pages/formulario.php' style='color:#000;text-decoration:underline;'>Voltar ao formulário</a></p>";
    echo "</div>";
    exit();
}

// Use a versão apenas com dígitos para salvar
$telefone = $telefone_digits;

/* =========================
   VERIFICAR DISPONIBILIDADE
========================= */

$verificar = $conn->prepare("
    SELECT id
    FROM reservas
    WHERE data_reserva = ?
    AND hora_reserva = ?
    AND mesa = ?
    AND estado != 'Cancelada'
");

$verificar->bind_param(
    "ssi",
    $data_reserva,
    $hora_reserva,
    $mesa
);

$verificar->execute();

$resultado = $verificar->get_result();

if ($resultado->num_rows > 0) {
    die("Esta mesa já está reservada neste horário.");
}

/* =========================
   GERAR CÓDIGO
========================= */

$codigo_reserva = "RES" . rand(10000, 99999);

$estado = "Pendente";
/* =========================
   CALCULAR TOTAL
========================= */
$total = 0;

foreach ($carrinho as $item) {
    $total += $item['preco'] * $item['qtd'];
}

$carrinhoJson = json_encode($carrinho);

/* =========================
   INSERIR RESERVA (COM CARRINHO)
========================= */
$stmt = $conn->prepare("
    INSERT INTO reservas
    (
        codigo_reserva,
        nome,
        email,
        telefone,
        data_reserva,
        hora_reserva,
        num_pessoa,
        mesa,
        estado
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "ssssssiss",
    $codigo_reserva,
    $nome,
    $email,
    $telefone,
    $data_reserva,
    $hora_reserva,
    $num_pessoa,
    $mesa,
    $estado
);
/* =========================
   EXECUTAR
========================= */

if ($stmt->execute()) {

    echo "
    <div style='
        background:#111;
        color:white;
        padding:20px;
        border-radius:15px;
        text-align:center;
        max-width:450px;
        margin:auto;
        font-family:Arial;
        margin-top:50px;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
    '>
        <h2 style='color:#28a745;'>
            Reserva feita com sucesso!
        </h2>
        <p>
            <strong>Código da reserva:</strong>
        </p>
        <h1 id='codigoReserva'
            style='
            color:#ff2b2b;
            letter-spacing:3px;
            '>
            $codigo_reserva
        </h1>
        <p>
            Guarde este código para consultar sua reserva.
        </p>
        <br>
        <!-- BOTÃO COPIAR -->
        <button onclick='copiarCodigo()'
            style='
            padding:12px 18px;
            border:none;
            background:#28a745;
            color:white;
            border-radius:10px;
            cursor:pointer;
            margin:5px;
            font-size:15px;
            '>
            Copiar Código
        </button>
        <!-- BOTÃO PDF -->
        <a href='gerar_codigo.php?codigo=$codigo_reserva'
           style='
           display:inline-block;
           padding:12px 18px;
           background:#ff2b2b;
           color:white;
           text-decoration:none;
           border-radius:10px;
           margin:5px;
           font-size:15px;
           '>
           Baixar
        </a>

        <br><br>

        <a href='../pages/formulario.php'
               style='
               color:white;
               text-decoration:underline;
               '>
               Voltar
            </a>
    </div>
    <script>

    function copiarCodigo() {

        let codigo =
        document.getElementById('codigoReserva').innerText;

        navigator.clipboard.writeText(codigo);

        alert('Código copiado com sucesso!');

    }

    </script>
    ";

} else {

    echo 'Erro ao reservar: ' . $stmt->error;

}

/* =========================
   FECHAR CONEXÕES
========================= */

$stmt->close();
$verificar->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <script src="../javascript/formulario.js"></script>
</body>
</html>