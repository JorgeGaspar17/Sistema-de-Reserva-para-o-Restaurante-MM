<?php

/* =========================
   CONEXÃO
========================= */

require_once __DIR__ . '/../restaurante.php';

/* =========================
   HEADER JSON
========================= */

header('Content-Type: application/json; charset=utf-8');

/* =========================
   PESQUISA
========================= */

$busca = trim($_GET['busca'] ?? '');

/* =========================
   QUERY BASE
========================= */

$sql = "
SELECT 
    id,
    nome,
    email,
    telefone,
    data_reserva,
    hora_reserva,
    num_pessoa,
    mesa,
    codigo_reserva
FROM reservas
";

/* =========================
   FILTRO DE PESQUISA
========================= */

if(!empty($busca)){

    $sql .= "
    WHERE 
        nome LIKE ? OR
        telefone LIKE ? OR
        codigo_reserva LIKE ?
    ";
}

/* =========================
   ORDER
========================= */

$sql .= "
ORDER BY id DESC
";

/* =========================
   PREPARE
========================= */

$stmt = $conn->prepare($sql);

/* =========================
   VALIDAR PREPARE
========================= */

if(!$stmt){

    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro na preparação da consulta."
    ]);

    exit;
}

/* =========================
   BIND
========================= */

if(!empty($busca)){

    $like = "%{$busca}%";

    $stmt->bind_param(
        "sss",
        $like,
        $like,
        $like
    );
}

/* =========================
   EXECUTAR
========================= */

$stmt->execute();

$result = $stmt->get_result();

/* =========================
   ARRAY
========================= */

$reservas = [];

/* =========================
   LOOP
========================= */

while($row = $result->fetch_assoc()){

    $reservas[] = [

        "id" => $row["id"],

        "nome" => htmlspecialchars($row["nome"]),

        "email" => htmlspecialchars($row["email"]),

        "telefone" => htmlspecialchars($row["telefone"]),

        "data_reserva" => $row["data_reserva"],

        "hora_reserva" => $row["hora_reserva"],

        "num_pessoa" => $row["num_pessoa"],

        "mesa" => $row["mesa"],

        "codigo_reserva" => $row["codigo_reserva"]
    ];
}

/* =========================
   RETORNO JSON
========================= */

echo json_encode([

    "status" => "sucesso",

    "total" => count($reservas),

    "reservas" => $reservas

], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

/* =========================
   FECHAR
========================= */

$stmt->close();
$conn->close();

?>