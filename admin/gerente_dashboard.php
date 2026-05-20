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

<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="main">

    <!-- TOPO -->
    <div class="topbar">

        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

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

const chartData = {
    statusLabels: ['Aprovadas', 'Pendentes', 'Canceladas'],
    statusValues: [<?= $aprovadas ?>, <?= $pendentes ?>, <?= $canceladas ?>],
    statusColors: ['#16a34a', '#f59e0b', '#dc2626'],
    productivityLabels: ['Hoje', 'Mês', 'Ano'],
    productivityValues: [<?= $reservas_hoje ?>, <?= $reservas_mes ?>, <?= $reservas_ano ?>]
};

function drawBarChart(canvas, labels, values, colors) {
    const ctx = canvas.getContext('2d');
    const width = canvas.clientWidth;
    const height = 280;
    const scale = window.devicePixelRatio || 1;
    canvas.width = width * scale;
    canvas.height = height * scale;
    ctx.setTransform(scale, 0, 0, scale, 0, 0);
    ctx.clearRect(0, 0, width, height);

    const padding = 40;
    const chartWidth = width - padding * 2;
    const chartHeight = height - padding * 2;
    const max = Math.max(...values, 1);

    ctx.fillStyle = '#f4f4f4';
    ctx.fillRect(padding, padding, chartWidth, chartHeight);

    ctx.strokeStyle = '#ccc';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 5; i++) {
        const y = padding + (chartHeight / 5) * i;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(padding + chartWidth, y);
        ctx.stroke();
    }

    const barWidth = chartWidth / values.length * 0.6;
    values.forEach((value, index) => {
        const barHeight = (value / max) * (chartHeight - 20);
        const x = padding + index * (chartWidth / values.length) + (chartWidth / values.length - barWidth) / 2;
        const y = padding + chartHeight - barHeight;

        ctx.fillStyle = colors[index] || '#333';
        ctx.fillRect(x, y, barWidth, barHeight);

        ctx.fillStyle = '#111';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(value, x + barWidth / 2, y - 10);
        ctx.fillText(labels[index], x + barWidth / 2, padding + chartHeight + 20);
    });
}

function drawLineChart(canvas, labels, values, color) {
    const ctx = canvas.getContext('2d');
    const width = canvas.clientWidth;
    const height = 280;
    const scale = window.devicePixelRatio || 1;
    canvas.width = width * scale;
    canvas.height = height * scale;
    ctx.setTransform(scale, 0, 0, scale, 0, 0);
    ctx.clearRect(0, 0, width, height);

    const padding = 40;
    const chartWidth = width - padding * 2;
    const chartHeight = height - padding * 2;
    const max = Math.max(...values, 1);

    ctx.fillStyle = '#f4f4f4';
    ctx.fillRect(padding, padding, chartWidth, chartHeight);

    ctx.strokeStyle = '#ccc';
    ctx.lineWidth = 1;
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

function renderDashboardCharts() {
    drawBarChart(document.getElementById('graficoStatus'), chartData.statusLabels, chartData.statusValues, chartData.statusColors);
    drawLineChart(document.getElementById('graficoProdutividade'), chartData.productivityLabels, chartData.productivityValues, '#d62828');
}

window.addEventListener('load', renderDashboardCharts);
window.addEventListener('resize', renderDashboardCharts);

const sidebar = document.querySelector('.sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');

function toggleSidebar(){
    const isOpen = sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('visible', isOpen);
    sidebar.classList.toggle('closed', !isOpen);
}

function closeSidebar(){
    sidebar.classList.remove('open');
    sidebar.classList.add('closed');
    sidebarOverlay.classList.remove('visible');
}

sidebarToggle.addEventListener('click', toggleSidebar);
sidebarOverlay.addEventListener('click', closeSidebar);
window.addEventListener('resize', () => {
    if(window.innerWidth > 1024){
        sidebar.classList.remove('closed', 'open');
        sidebarOverlay.classList.remove('visible');
    }
});

</script>
<script src="dashboard.js"></script>
<script src="formulario.js"></script>
</body>
</html>