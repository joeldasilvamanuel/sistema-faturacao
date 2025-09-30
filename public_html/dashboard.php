<?php
// Arquivo: supermercado-faturacao/public_html/dashboard.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php'; // Inclui a conexão PDO
iniciarSessao();

// VERIFICAÇÃO DE AUTORIZAÇÃO: Se não estiver logado, volta para o login
if (!isset($_SESSION['utilizador_id'])) {
    header("Location: index.php");
    exit();
}

// -------------------------------------------------------------
// BUSCA DE DADOS REAIS DA BASE DE DADOS (KPIs)
// -------------------------------------------------------------

$nome_utilizador = $_SESSION['nome_utilizador'];
$nome_role       = $_SESSION['nome_role'];
$is_admin        = $_SESSION['is_admin'];

// Variáveis que vão armazenar os dados dinâmicos
$total_utilizadores = 0;
$total_produtos_stock = 0;

try {
    $pdo = getConnection(); // Conecta à base de dados

    // 1. Contar o total de utilizadores
    $sql_users = "SELECT COUNT(id_utilizador) FROM utilizadores";
    $stmt_users = $pdo->query($sql_users);
    $total_utilizadores = $stmt_users->fetchColumn();

    // 2. Contar o total de UNIDADES de produtos em stock (soma das quantidades)
    $sql_stock = "SELECT COALESCE(SUM(stock), 0) FROM produtos";
    $stmt_stock = $pdo->query($sql_stock);
    $total_produtos_stock = $stmt_stock->fetchColumn();

    // As métricas de Vendas e Receita ficarão estáticas até criarmos as tabelas de faturação

} catch (PDOException $e) {
    // Regista o erro para auditoria, mas mantém a página funcional
    error_log("Erro ao carregar dados da Dashboard: " . $e->getMessage());
}

?>




<!DOCTYPE html>
<html lang="pt-ao">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo htmlspecialchars($nome_role); ?></title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- botão de recolher o sidebar -->
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="sidebar-header">
                <h2><i class="fas fa-store"></i> SuperFatura</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item active">
                        <a href="#">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="cadastrar_utilizadores.php">
                            <i class="fas fa-user-plus"></i>
                            <span>Cadastrar Utilizadores</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a href="gerir_utilizadores.php">
                            <i class="fas fa-user-cog"></i>
                            <span>Gerir Utilizadores</span>
                        </a>
                    </li> -->
                    <li class="nav-item">
                        <a href="cadastrar_produtos.php">
                            <i class="fas fa-boxes"></i>
                            <span>Cadastrar Produtos</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a href="gerir_produtos.php">
                            <i class="fas fa-warehouse"></i>
                            <span>Gerir Produtos</span>
                        </a>
                    </li> -->
                    <li class="nav-item">
                        <a href="relatorios.php">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#">
                            <i class="fas fa-users"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#">
                            <i class="fas fa-cog"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo htmlspecialchars($nome_utilizador); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($nome_role); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Section -->
                <section class="welcome-section">
                    <div class="welcome-card">
                        <h2>Bem-vindo, <?php echo htmlspecialchars($nome_utilizador); ?>!</h2>
                        <p>Seu cargo no sistema é: <strong><?php echo htmlspecialchars($nome_role); ?></strong>.</p>
                        <div class="welcome-actions">
                            <button class="btn-primary">
                                <i class="fas fa-plus"></i> Nova Venda
                            </button>
                            <button class="btn-secondary">
                                <i class="fas fa-chart-line"></i> Ver Relatórios
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Stats Cards -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Vendas Hoje</h3>
                                <span class="stat-value">24</span>
                                <span class="stat-change positive">+12%</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Receita Total</h3>
                                <span class="stat-value">1.245.750 Kz</span>
                                <span class="stat-change positive">+8%</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total em Stock</h3>
                                <span class="stat-value"><?php echo htmlspecialchars($total_produtos_stock); ?></span>
                                <span class="stat-change info">Unidades</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total de Utilizadores</h3>
                                <span class="stat-value"><?php echo htmlspecialchars($total_utilizadores); ?></span>
                                <span class="stat-change info">Registados</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Admin Panel -->
                <?php if ($is_admin): ?>
                <section class="admin-panel">
                    <h2>Painel de Administração do Sistema</h2>
                    <p>Ações de alto nível:</p>
                    <div class="admin-actions">
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h3>Cadastrar Utilizadores</h3>
                            <p>Adicione novos utilizadores ao sistema</p>
                            <a href="cadastrar_usuarios.php" class="btn-action">Aceder</a>
                        </div>
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h3>Cadastrar Produtos</h3>
                            <p>Adicione novos produtos ao inventário</p>
                            <a href="cadastrar_produtos.php" class="btn-action">Aceder</a>
                        </div>
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h3>Relatórios</h3>
                            <p>Aceda a relatórios e dados críticos</p>
                            <a href="relatorios.php" class="btn-action">Aceder</a>
                        </div>
                    </div>
                </section>
                <?php elseif ($nome_role === 'Operador de Caixa'): ?>
                <section class="cashier-panel">
                    <h2>Painel de Faturação</h2>
                    <p>Aceda aqui para iniciar uma nova venda.</p>
                    <div class="cashier-actions">
                        <button class="btn-primary large">
                            <i class="fas fa-cash-register"></i> Iniciar Nova Venda
                        </button>
                    </div>
                </section>
                <?php else: ?>
                <section class="viewer-panel">
                    <h2>Painel de Visualização</h2>
                    <p>Aguarde pelas funcionalidades do seu cargo.</p>
                </section>
                <?php endif; ?>

                <!-- Recent Activity -->
                <section class="activity-section">
                    <h2>Atividade Recente</h2>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-details">
                                <p>Nova venda realizada - <strong>Venda #2456</strong></p>
                                <span class="activity-time">Há 5 minutos</span>
                            </div>
                            <div class="activity-amount">15.750 Kz</div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-details">
                                <p>Novo utilizador cadastrado - <strong>Maria Silva</strong></p>
                                <span class="activity-time">Há 1 hora</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="activity-details">
                                <p>Produto adicionado ao stock - <strong>Arroz Tio João</strong></p>
                                <span class="activity-time">Há 2 horas</span>
                            </div>
                            <div class="activity-amount">50 unidades</div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="js/dashboard.js"></script>
</body>

</html>