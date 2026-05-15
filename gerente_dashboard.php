<?php
session_start();

/* =====================================
   PROTEGER DASHBOARD
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
   ESTATÍSTICAS GERAIS
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

$canceladas = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE estado='Cancelada'"
);

$pendentes = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE estado='Pendente'"
);

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

$reservas_hoje = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE data_reserva = CURDATE()"
);

$reservas_mes = total(
    $conn,
    "SELECT COUNT(*) 
     FROM reservas
     WHERE MONTH(data_reserva)=MONTH(CURDATE())
     AND YEAR(data_reserva)=YEAR(CURDATE())"
);

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

<title>Dashboard</title>

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

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
    margin-bottom:25px;
}

.topbar h1{
    color:#111;
    margin-bottom:5px;
}

.subtitulo{
    color:#666;
    font-size:14px;
}

.admin-box{
    background:#111;
    color:white;
    padding:12px 18px;
    border-radius:8px;
    font-size:14px;
    font-weight:bold;
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
    padding:20px;
    border-radius:10px;
    border-left:5px solid #d62828;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.card h3{
    font-size:15px;
    color:#555;
    margin-bottom:10px;
}

.card h1{
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
    margin-bottom:20px;
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
    max-height:300px;
}

/* =====================================
   INFO SISTEMA
===================================== */

.info-dashboard{
    margin-top:10px;
    font-size:14px;
    color:#666;
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
   MOBILE
===================================== */

@media(max-width:600px){

    .main{
        padding:15px;
    }

    .topbar{
        align-items:flex-start;
    }

    .card h1{
        font-size:24px;
    }

}

</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">

    <!-- TOPO -->
    <div class="topbar">

        <div>

            <h1>Dashboard</h1>

            <span class="subtitulo">
                Painel administrativo do restaurante
            </span>

            <div class="info-dashboard">

                Atualizado automaticamente • 
                <?= $data ?> às <?= $hora ?>

            </div>

        </div>

        <div class="admin-box">

            <?= htmlspecialchars($_SESSION['admin_nome']); ?>

        </div>

    </div>

    <!-- ESTATÍSTICAS -->
    <div class="cards">

        <div class="card">
            <h3>Total Reservas</h3>
            <h1><?= $total_reservas ?></h1>
        </div>

        <div class="card">
            <h3>Aprovadas</h3>
            <h1><?= $aprovadas ?></h1>
        </div>

        <div class="card">
            <h3>Canceladas</h3>
            <h1><?= $canceladas ?></h1>
        </div>

        <div class="card">
            <h3>Pendentes</h3>
            <h1><?= $pendentes ?></h1>
        </div>

        <div class="card">
            <h3>Total de Pratos</h3>
            <h1><?= $total_pratos ?></h1>
        </div>

        <div class="card">
            <h3>Total em Stock</h3>
            <h1><?= $total_stock ?></h1>
        </div>

    </div>

    <!-- PRODUTIVIDADE -->
    <div class="cards">

        <div class="card">
            <h3>Reservas Hoje</h3>
            <h1><?= $reservas_hoje ?></h1>
        </div>

        <div class="card">
            <h3>Reservas do Mês</h3>
            <h1><?= $reservas_mes ?></h1>
        </div>

        <div class="card">
            <h3>Reservas do Ano</h3>
            <h1><?= $reservas_ano ?></h1>
        </div>

    </div>

    <!-- GRÁFICOS -->
    <div class="graficos">

        <!-- STATUS -->
        <div class="grafico-box">

            <h2>Status das Reservas</h2>

            <canvas id="graficoStatus"></canvas>

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
   AUTO ATUALIZAÇÃO
===================================== */

setTimeout(() => {

    location.reload();

}, 60000);

/* =====================================
   STATUS RESERVAS
===================================== */

new Chart(document.getElementById("graficoStatus"), {

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
   PRODUTIVIDADE
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

            backgroundColor:"rgba(214,40,40,0.08)",

            fill:true,

            tension:0.3

        }]
    },

    options:{
        responsive:true
    }

});

</script>
<script src="dashboard.js"></script>
</body>
</html>