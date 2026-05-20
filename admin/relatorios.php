<?php
require_once __DIR__ . '/../includes/admin_init.php';

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

<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    min-height:460px;
}

.grafico-box h2{
    margin-bottom:15px;
    font-size:18px;
}

canvas{
    width:100%;
    max-height:360px;
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
        page-break-inside: avoid;
    }

    .graficos{
        grid-template-columns:1fr;
        gap:22px;
    }

    .cards{
        grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    }

    .topo{
        margin-bottom:20px;
    }

    canvas{
        max-height:340px;
    }
}

</style>

</head>

<body>

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

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

const reportData = {
    statusLabels: ['Aprovadas', 'Pendentes', 'Canceladas'],
    statusValues: [<?= $aprovadas ?>, <?= $pendentes ?>, <?= $canceladas ?>],
    statusColors: ['#16a34a', '#f59e0b', '#dc2626'],
    productivityLabels: ['Hoje', 'Mês', 'Ano'],
    productivityValues: [<?= $reservas_hoje ?>, <?= $reservas_mes ?>, <?= $reservas_ano ?>]
};

function drawBarChart(canvas, labels, values, colors) {
    const ctx = canvas.getContext('2d');
    const width = canvas.clientWidth;
    const height = 320;
    const scale = window.devicePixelRatio || 1;
    canvas.width = width * scale;
    canvas.height = height * scale;
    ctx.setTransform(scale, 0, 0, scale, 0, 0);
    ctx.clearRect(0, 0, width, height);

    const padding = 40;
    const chartWidth = width - padding * 2;
    const chartHeight = height - padding * 2;
    const max = Math.max(...values, 1);

    ctx.fillStyle = '#f9f9f9';
    ctx.fillRect(padding, padding, chartWidth, chartHeight);

    ctx.strokeStyle = '#dcdcdc';
    for (let i = 0; i <= 5; i++) {
        const y = padding + (chartHeight / 5) * i;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(padding + chartWidth, y);
        ctx.stroke();
    }

    const barWidth = chartWidth / values.length * 0.55;
    values.forEach((value, index) => {
        const barHeight = (value / max) * (chartHeight - 20);
        const x = padding + index * (chartWidth / values.length) + (chartWidth / values.length - barWidth) / 2;
        const y = padding + chartHeight - barHeight;

        ctx.fillStyle = colors[index] || '#333';
        ctx.fillRect(x, y, barWidth, barHeight);

        ctx.fillStyle = '#222';
        ctx.font = '13px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(value, x + barWidth / 2, y - 10);
        ctx.fillText(labels[index], x + barWidth / 2, padding + chartHeight + 18);
    });
}

function drawLineChart(canvas, labels, values, color) {
    const ctx = canvas.getContext('2d');
    const width = canvas.clientWidth;
    const height = 320;
    const scale = window.devicePixelRatio || 1;
    canvas.width = width * scale;
    canvas.height = height * scale;
    ctx.setTransform(scale, 0, 0, scale, 0, 0);
    ctx.clearRect(0, 0, width, height);

    const padding = 40;
    const chartWidth = width - padding * 2;
    const chartHeight = height - padding * 2;
    const max = Math.max(...values, 1);

    ctx.fillStyle = '#f9f9f9';
    ctx.fillRect(padding, padding, chartWidth, chartHeight);

    ctx.strokeStyle = '#dcdcdc';
    for (let i = 0; i <= 5; i++) {
        const y = padding + (chartHeight / 5) * i;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(padding + chartWidth, y);
        ctx.stroke();
    }

    ctx.strokeStyle = color;
    ctx.lineWidth = 3;
    ctx.beginPath();
    values.forEach((value, index) => {
        const x = padding + (chartWidth * index) / (values.length - 1);
        const y = padding + chartHeight - (value / max) * (chartHeight - 20);
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    ctx.stroke();

    ctx.fillStyle = color;
    values.forEach((value, index) => {
        const x = padding + (chartWidth * index) / (values.length - 1);
        const y = padding + chartHeight - (value / max) * (chartHeight - 20);
        ctx.beginPath();
        ctx.arc(x, y, 5, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillText(labels[index], x, padding + chartHeight + 18);
    });
}

function renderReportCharts() {
    drawBarChart(document.getElementById('graficoReservas'), reportData.statusLabels, reportData.statusValues, reportData.statusColors);
    drawLineChart(document.getElementById('graficoProdutividade'), reportData.productivityLabels, reportData.productivityValues, '#d62828');
}

window.addEventListener('load', renderReportCharts);
window.addEventListener('resize', renderReportCharts);

</script>

</body>
</html>