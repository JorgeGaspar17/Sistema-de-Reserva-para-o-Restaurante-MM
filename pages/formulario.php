<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Mesa</title>
    <link rel="stylesheet" href="../assets/css/formulario.css">

    <style>
        .form-card{max-width:720px;margin:30px auto;padding:22px;border-radius:12px;background:#fff;box-shadow:0 6px 30px rgba(0,0,0,0.06);}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .field-inline{display:flex;gap:12px}
        .hp{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden}
        .note{font-size:14px;color:#444;margin-top:-8px;margin-bottom:12px}
        @media(max-width:700px){.grid-2{grid-template-columns:1fr}}
    </style>
</head>

<body>

    <nav>
        <ul class="nav-links">
            <li><a href="index.html">Início</a></li>
            <li><a href="../pages/cardapio.html">Cardápio</a></li>
            <li><a href="../pages/sobre.html">Sobre</a></li>
        </ul>
    </nav>

    <div class="form-card">

        <h2>Reserva Agora</h2>

        <!-- FORM RESERVA -->
        <form action="../admin/reservas.php" method="POST" id="formReserva" autocomplete="off">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
            <input type="text" name="hp" class="hp" tabindex="-1" autocomplete="off">

            <div class="grid-2">
                <div>
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" placeholder="Nome Completo" required>
                </div>

                <div>
                    <label for="num_pessoa">Nº Pessoas</label>
                    <input type="number" id="num_pessoa" name="num_pessoa" placeholder="1-10" min="1" max="10" required>
                </div>
            </div>

            <div class="field-inline">
                <div style="flex:1">
                    <label for="email">E-mail (opcional)</label>
                    <input type="email" id="email" name="email" placeholder="E-mail">
                </div>
                <div style="flex:1">
                    <label for="telefone">Telefone (opcional)</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="Telefone">
                </div>
            </div>

            <p class="note">Informe pelo menos um contato: e-mail ou telefone.</p>

            <div class="grid-2">
                <div>
                    <label for="data_reserva">Data</label>
                    <input type="date" id="data_reserva" name="data_reserva" required>
                </div>
                <div>
                    <label for="hora_reserva">Hora</label>
                    <input type="time" id="hora_reserva" name="hora_reserva" min="08:00" max="22:00" required>
                </div>
            </div>

            <div>
                <label for="mesa">Mesa</label>
                <select name="mesa" id="mesa" required>
                    <option value="">Selecionar Mesa</option>
                    <option value="1">Mesa 1</option>
                    <option value="2">Mesa 2</option>
                    <option value="3">Mesa 3</option>
                    <option value="4">Mesa 4</option>
                    <option value="5">Mesa 5</option>
                </select>
            </div>

            <div style="display:flex;gap:12px;margin-top:18px;">
                <button type="button" class="btn-voltar" onclick="window.location.href='index.html'">Voltar</button>
                <button type="submit" class="btn-enviar">Reservar</button>
            </div>

        </form>

        <br>
        <!-- VER RESERVA -->
        <h2>Ver Reserva</h2>

        <form action="../admin/ver_reserva.php" method="GET" style="margin-top:8px;">
            <div class="grid-2">
                <input type="text" name="codigo_reserva" placeholder="Código da reserva">
                <input type="email" name="email" placeholder="E-mail">
            </div>
            <div style="margin-top:8px">
                <input type="tel" name="telefone" placeholder="Telefone">
            </div>
            <div class="buttons" style="margin-top:12px;">
                <button type="submit" class="btn-enviar">Verificar Reserva</button>
            </div>
        </form>

    <script src="../javascript/formulario.js"></script>
    <script src="../javascript/gerente.js"></script>
</body>

</html>
