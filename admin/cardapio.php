<?php
require_once __DIR__ . '/../includes/admin_init.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio</title>
    <link rel="stylesheet" href="../assets/css/cardapio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="main">
    <div class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div>
            <h1>Cardápio do Restaurante</h1>
            <span class="subtitulo">Visualize os pratos disponíveis no sistema</span>
        </div>
    </div>

    <section class="menu-category">
        <div id="lista-pratos"></div>
    </section>
</div>

<script>
    fetch('../includes/listar_pratos.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('lista-pratos').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('lista-pratos').innerHTML = '<p>Erro ao carregar os pratos.</p>';
            console.error(error);
        });
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

if(sidebarToggle){ sidebarToggle.addEventListener('click', toggleSidebar); }
if(sidebarOverlay){ sidebarOverlay.addEventListener('click', closeSidebar); }
window.addEventListener('resize', () => {
    if(window.innerWidth > 1024){
        sidebar.classList.remove('closed', 'open');
        sidebarOverlay.classList.remove('visible');
    }
});
</script>

</body>
</html>