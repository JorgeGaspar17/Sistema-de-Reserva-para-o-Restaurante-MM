<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: ../admin/gerente_dashboard.php');
    exit();
}

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

$error = '';
if (!empty($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty':
            $error = 'Preencha todos os campos.';
            break;
        case 'invalid_email':
            $error = 'Email inválido.';
            break;
        case 'csrf':
            $error = 'Falha de segurança. Tente novamente.';
            break;
        case 'credentials':
            $error = 'Nome, email ou senha incorretos.';
            break;
        default:
            $error = 'Erro ao tentar fazer login.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Gerente - Restaurante MM</title>
    <link rel="stylesheet" href="../assets/css/gerente_login.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>

<body>

    <nav>
        <ul class="nav-links">
            <li><a href="index.html">Início</a></li>
            <li><a href="cardapio.html">Cardápio</a></li>
            <li><a href="sobre.html">Sobre</a></li>
        </ul>
    </nav>

    <div class="login-container">
        <h1>Gerente</h1>
        <p>Entrar no painel do Restaurante MM</p>

        <?php if ($error): ?>
            <div class="login-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form action="../admin/login.php" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="input-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" placeholder="Digite o nome" required maxlength="50" minlength="3" autocomplete="off">
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Digite o email" required maxlength="100" autocomplete="off">
            </div>

            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite a senha" required minlength="6" maxlength="100" autocomplete="new-password">
            </div>

            <button class="btn-login" type="submit">Entrar</button>
        </form>
    </div>

    <script src="../javascript/gerente.js"></script>
</body>

</html>
