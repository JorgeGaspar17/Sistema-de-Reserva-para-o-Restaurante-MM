<?php
session_start();

/* =========================
   PROTEGER DASHBOARD
========================= */

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../pages/gerente_login.html");
    exit();
}

/* =========================
   CONEXÃO
========================= */

$conn = new mysqli("localhost", "root", "", "restaurante");

if ($conn->connect_error) {
    die("Erro de conexão.");
}

$conn->set_charset("utf8mb4");

// garantir token CSRF para ações administrativas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Tratar ações via POST (mais seguro)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = $_POST['csrf_token'] ?? '';
    if (empty($posted) || !hash_equals($_SESSION['csrf_token'], $posted)) {
        header('HTTP/1.1 400 Bad Request');
        die('Token CSRF inválido');
    }

    if (isset($_POST['aprovar'])) {
        $id = intval($_POST['aprovar']);
        $stmt = $conn->prepare("UPDATE reservas SET estado='Aprovada' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: dashboard_reserva.php"); exit();
    }

    if (isset($_POST['cancelar'])) {
        $id = intval($_POST['cancelar']);
        $stmt = $conn->prepare("UPDATE reservas SET estado='Cancelada' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: dashboard_reserva.php"); exit();
    }

    if (isset($_POST['excluir'])) {
        $id = intval($_POST['excluir']);
        $stmt = $conn->prepare("DELETE FROM reservas WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: dashboard_reserva.php"); exit();
    }
}

/* =========================
   APROVAR
========================= */

if (isset($_GET['aprovar'])) {

    $id = intval($_GET['aprovar']);

    $stmt = $conn->prepare("
        UPDATE reservas
        SET estado='Aprovada'
        WHERE id=?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard_reserva.php");
    exit();
}

/* =========================
   CANCELAR
========================= */

if (isset($_GET['cancelar'])) {

    $id = intval($_GET['cancelar']);

    $stmt = $conn->prepare("
        UPDATE reservas
        SET estado='Cancelada'
        WHERE id=?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard_reserva.php");
    exit();
}

/* =========================
   EXCLUIR
========================= */

if (isset($_GET['excluir'])) {

    $id = intval($_GET['excluir']);

    $stmt = $conn->prepare("
        DELETE FROM reservas
        WHERE id=?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard_reserva.php");
    exit();
}

/* =========================
   EDITAR RESERVA
========================= */

if (isset($_POST['editar_reserva'])) {

    $id            = intval($_POST['id']);
    $nome          = trim($_POST['nome']);
    $telefone      = trim($_POST['telefone']);
    $num_pessoa    = intval($_POST['num_pessoa']);
    $mesa          = trim($_POST['mesa']);
    $data_reserva  = $_POST['data_reserva'];
    $hora_reserva  = $_POST['hora_reserva'];
    $estado        = trim($_POST['estado']);

    $stmt = $conn->prepare("
        UPDATE reservas
        SET 
            nome=?,
            telefone=?,
            num_pessoa=?,
            mesa=?,
            data_reserva=?,
            hora_reserva=?,
            estado=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssissssi",
        $nome,
        $telefone,
        $num_pessoa,
        $mesa,
        $data_reserva,
        $hora_reserva,
        $estado,
        $id
    );

    $stmt->execute();

    header("Location: dashboard_reserva.php");
    exit();
}

/* =========================
   LISTAR
========================= */

$reservas = $conn->query("
    SELECT *
    FROM reservas
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="pt">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Reservas</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>

/* =========================
Estilizando Dashboard Reserva
========================= */

.main{
    padding:25px;
}

/* =========================
   TOPO
========================= */

.topbar{
    margin-bottom:20px;
}

.topbar h1{
    color: #111;
    margin: bottom 5px;
}

.subtitulo{
    color: #666;
    font-size:14px;
}

/* =========================
   TABELA
========================= */

.table-box{
    width:100%;
    overflow-x: auto;
    background: white;
    border-radius:12px;
    box-shadow:1px 2px 10px rgba(0, 0, 0, 0.25);
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:1100px;
}

thead{
    background: #111;
    color: #ffff;
}

th{
    padding:16px;
    text-align:left;
    font-size:14px;
}

td{
    padding:14px 16px;
    border-bottom:1px solid #eee;
    font-size:14px;
}


/* =========================
   STATUS
========================= */

.status{
    padding:7px 12px;
    border-radius:20px;
    color:white;
    font-size:12px;
    font-weight:bold;
}

.aprovada{
    background:#16a34a;
}

.cancelada{
    background:#dc2626;
}

.pendente{
    background:#f59e0b;
}

/* =========================
   AÇÕES
========================= */

.acoes{
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
}

.btn{
    padding:6px 10px;
    text-decoration:none;
    color:white;
    font-size:10px;
    font-weight: bold;
    transition:0.5s;
}

.btn:hover{
    opacity:0.90;
}

.aprovar{
    background: #16a34a;
}

.cancelar{
    background: #f59e0b;
}

.editar{
    background: #0e7efd;
}

.excluir{
    background: #dc2626;
}

/* =========================
   MODAL
========================= */

.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    padding:20px;
    z-index:999;
}

.modal-content{
    background:white;
    width:100%;
    max-width:600px;
    border-radius:12px;
    padding:25px;
}

.modal-content h2{
    margin-bottom:20px;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group.full{
    grid-column:1/3;
}

.form-group label{
    margin-bottom:6px;
    font-size:14px;
    font-weight:bold;
}

.form-group input,
.form-group select{
    padding:12px;
    border:1px solid #ccc;
    border-radius:8px;
    outline:none;
}

.modal-buttons{
    margin-top:20px;
    display:flex;
    gap:10px;
}

.salvar{
    background:#16a34a;
    border:none;
    color:white;
    padding:12px 18px;
    border-radius:8px;
    cursor:pointer;
}

.fechar{
    background:#dc2626;
    border:none;
    color:white;
    padding:12px 18px;
    border-radius:8px;
    cursor:pointer;
}

/* =========================
   RESPONSIVO
========================= */

@media(max-width:768px){

    .form-grid{
        grid-template-columns:1fr;
    }

    .form-group.full{
        grid-column:auto;
    }

    .acoes{
        flex-direction:column;
    }

    .btn{
        text-align:center;
    }
}

</style>

</head>

<body>

<?php include "../includes/sidebar.php"; ?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="main">

    <div class="topbar">

        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div>
            <h1>Gestão de Reservas</h1>

            <span class="subtitulo">
                Controle e edição das reservas do restaurante
            </span>
        </div>

    </div>

    <div class="table-box">

        <table>

            <thead>

                <tr>

                    <th>Cliente</th>
                    <th>Telefone</th>
                    <th>Pessoas</th>
                    <th>Mesa</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Ações</th>

                </tr>

            </thead>

            <tbody>

            <?php while($r = $reservas->fetch_assoc()) { ?>

                <?php

                $classe = "pendente";

                if($r['estado'] == "Aprovada"){
                    $classe = "aprovada";
                }

                if($r['estado'] == "Cancelada"){
                    $classe = "cancelada";
                }

                ?>

                <tr>

                    <td><?= htmlspecialchars($r['nome']) ?></td>

                    <td><?= htmlspecialchars($r['telefone']) ?></td>

                    <td><?= htmlspecialchars($r['num_pessoa']) ?></td>

                    <td><?= htmlspecialchars($r['mesa']) ?></td>

                    <td><?= htmlspecialchars($r['data_reserva']) ?></td>

                    <td><?= htmlspecialchars($r['hora_reserva']) ?></td>

                    <td>
                        <span class="status <?= $classe ?>">
                            <?= htmlspecialchars($r['estado']) ?>
                        </span>
                    </td>

                    <td class="acoes">

                        <form method="POST" style="display:inline-block;margin:0">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" name="aprovar" value="<?= $r['id'] ?>" class="btn aprovar" title="Aprovar" aria-label="Aprovar">
                                <span class="icon"><i class="fa-solid fa-check"></i></span>
                            </button>
                        </form>

                        <form method="POST" style="display:inline-block;margin:0">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" name="cancelar" value="<?= $r['id'] ?>" class="btn cancelar" title="Cancelar" aria-label="Cancelar">
                                <span class="icon"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                        </form>

                        <button class="btn editar" title="Editar reserva" aria-label="Editar reserva" onclick="abrirModal(
                            '<?= $r['id'] ?>',
                            '<?= htmlspecialchars($r['nome'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($r['telefone'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($r['num_pessoa'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($r['mesa'], ENT_QUOTES) ?>',
                            '<?=htmlspecialchars($r['data_reserva']) ?>',
                            '<?= htmlspecialchars($r['hora_reserva']) ?>',
                            '<?=htmlspecialchars($r['estado']) ?>'
                        )">
                            <span class="icon"><i class="fa-solid fa-pen-to-square"></i></span>
                        </button>

                        <form method="POST" style="display:inline-block;margin:0" onsubmit="return confirm('Deseja excluir esta reserva?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" name="excluir" value="<?= $r['id'] ?>" class="btn excluir" title="Excluir" aria-label="Excluir">
                                <span class="icon"><i class="fa-solid fa-trash"></i></span>
                            </button>
                        </form>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<!-- MODAL -->
<div class="modal" id="modalEditar">

    <div class="modal-content">

        <h2>Editar Reserva</h2>

        <form method="POST">

            <input type="hidden" name="id" id="id">

            <div class="form-grid">

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" id="nome" required>
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="telefone" required>
                </div>

                <div class="form-group">
                    <label>Pessoas</label>
                    <input type="number" name="num_pessoa" id="num_pessoa" required>
                </div>

                <div class="form-group">
                    <label>Mesa</label>
                    <input type="text" name="mesa" id="mesa" required>
                </div>

                <div class="form-group">
                    <label>Data</label>
                    <input type="date" name="data_reserva" id="data_reserva" required>
                </div>

                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora_reserva" id="hora_reserva" required>
                </div>

                <div class="form-group full">
                    <label>Estado</label>

                    <select name="estado" id="estado">

                        <option value="Pendente">Pendente</option>

                        <option value="Aprovada">
                            Aprovada
                        </option>

                        <option value="Cancelada">
                            Cancelada
                        </option>

                    </select>
                </div>

            </div>

            <div class="modal-buttons">

                <button type="submit"
                name="editar_reserva"
                class="salvar">

                    Salvar Alterações

                </button>

                <button type="button"
                class="fechar"
                onclick="fecharModal()">

                    Fechar

                </button>

            </div>

        </form>

    </div>

</div>

<script>

function abrirModal(
    id,
    nome,
    telefone,
    num_pessoa,
    mesa,
    data,
    hora,
    estado
){

    document.getElementById("modalEditar").style.display = "flex";

    document.getElementById("id").value = id;
    document.getElementById("nome").value = nome;
    document.getElementById("telefone").value = telefone;
    document.getElementById("num_pessoa").value = num_pessoa;
    document.getElementById("mesa").value = mesa;
    document.getElementById("data_reserva").value = data;
    document.getElementById("hora_reserva").value = hora;
    document.getElementById("estado").value = estado;
}

function fecharModal(){

    document.getElementById("modalEditar").style.display = "none";
}

</script>

<script>
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

if(sidebarToggle){
    sidebarToggle.addEventListener('click', toggleSidebar);
}
if(sidebarOverlay){
    sidebarOverlay.addEventListener('click', closeSidebar);
}
window.addEventListener('resize', () => {
    if(window.innerWidth > 1024){
        sidebar.classList.remove('closed', 'open');
        sidebarOverlay.classList.remove('visible');
    }
});
</script>

</body>
</html>