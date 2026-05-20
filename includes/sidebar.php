<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="fa-solid fa-utensils"></i></span>
        <div>
            <h2>Restaurante MM</h2>
            <p>Área administrativa</p>
        </div>
    </div>

    <ul>
        <li class="<?= active_nav('gerente_dashboard.php') ?>">
            <a href="../admin/gerente_dashboard.php">
                <span class="icon"><i class="fa-solid fa-gauge-high"></i></span>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= active_nav('dashboard_reserva.php') ?>">
            <a href="../admin/dashboard_reserva.php">
                <span class="icon"><i class="fa-solid fa-eye"></i></span>
                <span>Reservas</span>
            </a>
        </li>
        <li class="<?= active_nav('pratos.php') ?>">
            <a href="../admin/pratos.php">
                <span class="icon"><i class="fa-solid fa-bowl-food"></i></span>
                <span>Pratos</span>
            </a>
        </li>
        <li class="<?= active_nav('relatorios.php') ?>">
            <a href="../admin/relatorios.php">
                <span class="icon"><i class="fa-solid fa-chart-column"></i></span>
                <span>Relatórios</span>
            </a>
        </li>
        <li>
            <a href="../logout.php">
                <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                <span>Sair</span>
            </a>
        </li>
    </ul>
</aside>