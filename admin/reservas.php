<?php
include ("restaurante.php");

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

        <a href='formulario.html'
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
    <script src="formulario.js"></script>
</body>
</html>