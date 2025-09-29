<!-- <!DOCTYPE html>
<html lang="pt-ao">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Supermercado Angola</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="processa_login.php" method="POST">
            <input type="text" name="username" placeholder="Nome de utilizador" required>
            <input type="password" name="password" placeholder="Palavra-passe" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>

</html> -->


<?php
// Arquivo: supermercado-faturacao/public_html/index.php

require_once '../inc/funcoes.php';
iniciarSessao();

// Se o utilizador já estiver logado, redireciona-o
if (isset($_SESSION['utilizador_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Obtém e limpa a mensagem de erro
$erro_login = isset($_SESSION['erro_login']) ? $_SESSION['erro_login'] : '';
unset($_SESSION['erro_login']);
?>

<!DOCTYPE html>
<html lang="pt-ao">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Supermercado Angola</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
    .erro {
        color: #cc0000;
        background-color: #ffe0e0;
        border: 1px solid #cc0000;
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        margin-bottom: 15px;

        /* Fixar no topo */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>

        <?php if ($erro_login): ?>
        <p class="erro"><?php echo htmlspecialchars($erro_login); ?></p>
        <?php endif; ?>

        <form action="processa_login.php" method="POST">
            <input type="text" name="nome_utilizador" placeholder="Nome de utilizador" required>
            <input type="password" name="password" placeholder="Palavra-passe" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>

</html>