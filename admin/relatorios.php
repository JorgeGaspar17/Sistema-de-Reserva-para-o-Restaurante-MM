<?php
session_start();

/* =====================================
   PROTEGER SISTEMA
===================================== */

if (!isset($_SESSION['admin_id'])) {
    header("Location: gerente_login.html");
    exit();
}

/* =====================================
   CONEXÃO
===================================== */

$conn = new mysqli("localhost", "root", "", "restaurante");

if ($conn->connect_error) {
    die("Erro de conexão.");
}

$conn->set_charset("utf8mb4");

/* =====================================
   FUNÇÃO TOTAL
===================================== */

function total($conn, $sql)
{
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return (int) array_values($row)[0];
    }

    return 0;
}

/* =====================================
   RESERVAS GERAIS
===================================== */

$total_reservas = total(
    $conn,
    "SELECT COUNT(*) FROM reservas"
);

$aprovadas = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE estado='Aprovada'"
);

$pendentes = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE estado='Pendente'"
);

$canceladas = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE estado='Cancelada'"
);

/* =====================================
   PRATOS
===================================== */

$total_pratos = total(
    $conn,
    "SELECT COUNT(*) FROM pratos"
);

$total_stock = total(
    $conn,
    "SELECT IFNULL(SUM(qtdprato),0) 
     FROM pratos"
);

/* =====================================
   PRODUTIVIDADE
===================================== */

/* HOJE */
$reservas_hoje = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE data_reserva = CURDATE()"
);

/* MÊS */
$reservas_mes = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE MONTH(data_reserva)=MONTH(CURDATE())
     AND YEAR(data_reserva)=YEAR(CURDATE())"
);

/* ANO */
$reservas_ano = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE YEAR(data_reserva)=YEAR(CURDATE())"
);

$data = date("d/m/Y");
$hora = date("H:i:s");

?>

<!DOCTYPE html>
<html lang="pt">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Relatórios</title>

<link rel="stylesheet" href="dashboard.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* =====================================
   MAIN
===================================== */

.main{
    padding:25px;
}

/* =====================================
   TOPO
===================================== */

.topo{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    margin-bottom:25px;
    flex-wrap:wrap;
}

.topo h1{
    color:#111;
    margin-bottom:5px;
}

.info{
    font-size:14px;
    color:#666;
}

/* =====================================
   BOTÃO
===================================== */

.btn-pdf{
    background:#d62828;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
    font-weight:bold;
}

.btn-pdf:hover{
    background:#b71c1c;
}

/* =====================================
   CARDS
===================================== */

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:15px;
    margin-bottom:25px;
}

.card{
    background:white;
    border-radius:10px;
    padding:20px;
    border-left:5px solid #d62828;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.card h3{
    font-size:15px;
    color:#555;
    margin-bottom:10px;
}

.card h2{
    font-size:28px;
    color:#111;
}

/* =====================================
   GRÁFICOS
===================================== */

.graficos{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.grafico-box{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.grafico-box h2{
    margin-bottom:15px;
    font-size:18px;
}

canvas{
    max-height:280px;
}

/* =====================================
   RESPONSIVO
===================================== */

@media(max-width:900px){

    .graficos{
        grid-template-columns:1fr;
    }

}

/* =====================================
   PDF
===================================== */

@media print{

    body{
        background:white;
    }

    .sidebar{
        display:none;
    }

    .btn-pdf{
        display:none;
    }

    .main{
        width:100%;
        padding:0;
    }

    .card,
    .grafico-box{
        box-shadow:none;
        border:1px solid #ccc;
    }

    .graficos{
        grid-template-columns:1fr;
    }
}

</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">

    <!-- TOPO -->
    <div class="topo">

        <div>

            <h1>Relatórios</h1>

            <div class="info">

                Atualizado automaticamente • 
                <?= $data ?> às <?= $hora ?>

            </div>

        </div>

        <button class="btn-pdf"
        onclick="window.print()">

            Gerar PDF

        </button>

    </div>

    <!-- CARDS -->
    <div class="cards">

        <div class="card">
            <h3>Total de Reservas</h3>
            <h2><?= $total_reservas ?></h2>
        </div>

        <div class="card">
            <h3>Aprovadas</h3>
            <h2><?= $aprovadas ?></h2>
        </div>

        <div class="card">
            <h3>Pendentes</h3>
            <h2><?= $pendentes ?></h2>
        </div>

        <div class="card">
            <h3>Canceladas</h3>
            <h2><?= $canceladas ?></h2>
        </div>

        <div class="card">
            <h3>Total de Pratos</h3>
            <h2><?= $total_pratos ?></h2>
        </div>

        <div class="card">
            <h3>Total em Stock</h3>
            <h2><?= $total_stock ?></h2>
        </div>

    </div>

    <!-- PRODUTIVIDADE -->
    <div class="cards">

        <div class="card">
            <h3>Reservas Hoje</h3>
            <h2><?= $reservas_hoje ?></h2>
        </div>

        <div class="card">
            <h3>Reservas do Mês</h3>
            <h2><?= $reservas_mes ?></h2>
        </div>

        <div class="card">
            <h3>Reservas do Ano</h3>
            <h2><?= $reservas_ano ?></h2>
        </div>

    </div>

    <!-- GRÁFICOS -->
    <div class="graficos">

        <!-- RESERVAS -->
        <div class="grafico-box">

            <h2>Status das Reservas</h2>

            <canvas id="graficoReservas"></canvas>

        </div>

        <!-- PRODUTIVIDADE -->
        <div class="grafico-box">

            <h2>Produtividade</h2>

            <canvas id="graficoProdutividade"></canvas>

        </div>

    </div>

</div>

<script>

/* =====================================
   ATUALIZAR AUTOMATICAMENTE
===================================== */

setTimeout(() => {

    location.reload();

}, 60000);

/* =====================================
   GRÁFICO RESERVAS
===================================== */

new Chart(document.getElementById("graficoReservas"), {

    type: "bar",

    data: {

        labels: [
            "Aprovadas",
            "Pendentes",
            "Canceladas"
        ],

        datasets: [{

            data: [
                <?= $aprovadas ?>,
                <?= $pendentes ?>,
                <?= $canceladas ?>
            ],

            backgroundColor: [
                "#16a34a",
                "#f59e0b",
                "#dc2626"
            ],

            borderRadius:5

        }]
    },

    options: {

        responsive:true,

        plugins:{
            legend:{
                display:false
            }
        }
    }

});

/* =====================================
   GRÁFICO PRODUTIVIDADE
===================================== */

new Chart(document.getElementById("graficoProdutividade"), {

    type: "line",

    data: {

        labels: [
            "Hoje",
            "Mês",
            "Ano"
        ],

        datasets: [{

            label:"Reservas",

            data: [
                <?= $reservas_hoje ?>,
                <?= $reservas_mes ?>,
                <?= $reservas_ano ?>
            ],

            borderColor:"#d62828",

            backgroundColor:"rgba(214,40,40,0.1)",

            fill:true,

            tension:0.3

        }]
    },

    options:{
        responsive:true
    }

});

</script>

</body>
</html>