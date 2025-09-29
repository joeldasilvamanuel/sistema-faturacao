<!DOCTYPE html>
<html lang="pt-ao">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo htmlspecialchars($nome_role); ?></title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($nome_utilizador); ?>!</h1>
        <p>Seu cargo no sistema é: **<?php echo htmlspecialchars($nome_role); ?>**.</p>

        <hr>

        <?php if ($is_admin): ?>
        <h2>Painel de Administração do Sistema</h2>
        <p>Ações de alto nível:</p>
        <ul>
            <li><a href="cadastrar_usuarios.php">1. Cadastrar Novos Utilizadores</a></li>
            <li><a href="cadastrar_produtos.php">2. Cadastrar Produtos</a></li>
            <li><a href="relatorios.php">3. Aceder a Relatórios e Dados Críticos</a></li>
        </ul>
        <?php elseif ($nome_role === 'Operador de Caixa'): ?>
        <h2>Painel de Faturação</h2>
        <p>Aceda aqui para iniciar uma nova venda.</p>
        <?php else: ?>
        <h2>Painel de Visualização</h2>
        <p>Aguarde pelas funcionalidades do seu cargo.</p>
        <?php endif; ?>

        <hr>
        <p><a href="logout.php">Sair do Sistema (Logout)</a></p>
    </div>
</body>

</html>