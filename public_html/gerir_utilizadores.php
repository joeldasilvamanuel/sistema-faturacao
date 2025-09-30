<?php
// Arquivo: public_html/gerir_utilizadores.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// Proteção: Garante que só administradores logados podem aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

$utilizadores = [];
$mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? '';
$mensagem_erro    = $_SESSION['mensagem_erro'] ?? '';
unset($_SESSION['mensagem_sucesso'], $_SESSION['mensagem_erro']);

try {
    $pdo = getConnection();

    // SQL para selecionar TODOS os utilizadores.
    // Fazemos um JOIN com a tabela 'roles' para mostrar o nome do cargo.
    $sql = "SELECT u.id_utilizador, u.nome_utilizador, u.password_hash, u.data_criacao, r.nome_role
            FROM utilizadores u
            INNER JOIN roles r ON u.id_role = r.id_role
            ORDER BY u.nome_utilizador ASC";

    $stmt = $pdo->query($sql);
    $utilizadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensagem_erro = "Erro ao carregar a lista de utilizadores: " . $e->getMessage();
    error_log("Erro em gerir_utilizadores.php: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-ao">

<head>
    <meta charset="UTF-8">
    <title>Gerir Utilizadores - Painel Admin</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #27ae27;
        --success-color: #27ae60;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --light-color: #ecf0f1;
        --dark-color: #27ae60;
        --text-color: #27ae27;
        --border-color: #bdc3c7;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    * {
        box-sizing: border-box;
    }

    .container {
        max-width: 95%;
        margin: 30px auto;
        padding: 0 20px;
    }

    .cabecalho-gerir {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-color);
    }

    .cabecalho-gerir h1 {
        color: var(--secondary-color);
        margin: 0;
        font-size: 2rem;
        font-weight: 600;
    }

    .btn-novo {
        background: linear-gradient(135deg, var(--success-color), #2ecc71);
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: var(--shadow);
    }

    .btn-novo:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, #2ecc71, var(--success-color));
    }

    .tabela-dados {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .tabela-dados th,
    .tabela-dados td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .tabela-dados th {
        background: linear-gradient(135deg, var(--secondary-color), var(--dark-color));
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .tabela-dados tr {
        transition: var(--transition);
    }

    .tabela-dados tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    .tabela-dados tr:last-child td {
        border-bottom: none;
    }

    .acoes {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .acoes a {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .acoes a:first-child {
        background-color: rgba(52, 152, 219, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(52, 152, 219, 0.3);
    }

    .acoes a:first-child:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-1px);
    }

    .btn-apagar {
        background-color: rgba(231, 76, 60, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(231, 76, 60, 0.3);
    }

    .btn-apagar:hover {
        background-color: var(--danger-color);
        color: white;
        transform: translateY(-1px);
    }

    .sucesso,
    .erro {
        padding: 15px 20px;
        margin-bottom: 25px;
        border-radius: 8px;
        font-weight: 500;
        border-left: 4px solid;
        box-shadow: var(--shadow);
    }

    .sucesso {
        background-color: rgba(39, 174, 96, 0.1);
        color: #155724;
        border-left-color: var(--success-color);
    }

    .erro {
        background-color: rgba(231, 76, 60, 0.1);
        color: #721c24;
        border-left-color: var(--danger-color);
    }

    /* Estilo para células de preço */
    .tabela-dados td:nth-child(4),
    .tabela-dados td:nth-child(5) {
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    .tabela-dados td:nth-child(5) {
        color: var(--success-color);
        background-color: rgba(39, 174, 96, 0.05);
    }

    /* Estilo para stock */
    .tabela-dados td:nth-child(6) {
        font-weight: 600;
        text-align: center;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .container {
            max-width: 100%;
            margin: 15px auto;
            padding: 0 15px;
        }

        .cabecalho-gerir {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .cabecalho-gerir h1 {
            font-size: 1.5rem;
        }

        .tabela-dados {
            font-size: 0.85rem;
        }

        .tabela-dados th,
        .tabela-dados td {
            padding: 10px 8px;
        }

        .acoes {
            flex-direction: column;
            gap: 5px;
        }

        .acoes a {
            justify-content: center;
            padding: 6px 8px;
        }
    }

    /* Link voltar ao painel */
    .container>p:last-child {
        margin-top: 30px;
        text-align: center;
    }

    .container>p:last-child a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        padding: 10px 20px;
        border: 1px solid var(--primary-color);
        border-radius: 6px;
        transition: var(--transition);
    }

    .container>p:last-child a:hover {
        background-color: var(--primary-color);
        color: white;
    }

    /* Mensagem quando não há produtos */
    .container>p:first-of-type {
        text-align: center;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 10px;
        color: var(--text-color);
        font-size: 1.1rem;
    }

    .container>p:first-of-type a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .container>p:first-of-type a:hover {
        text-decoration: underline;
    }
    </style>
    </style>
</head>

<body>
    <div class="container">

        <div class="cabecalho-gerir">
            <h1 style="color: #272223;">
                Gestão de Utilizadores: <?= count($utilizadores) ?> utilizadores cadastrados no sitema
            </h1>
            <a href="cadastrar_utilizadores.php" class="btn-novo">
                <i class="fas fa-user-plus"></i> Novo Utilizador
            </a>
        </div>

        <?php if ($mensagem_sucesso): ?>
        <p class="sucesso"><?= htmlspecialchars($mensagem_sucesso) ?></p>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
        <p class="erro"><?= htmlspecialchars($mensagem_erro) ?></p>
        <?php endif; ?>

        <?php if (empty($utilizadores)): ?>
        <p>Nenhum utilizador cadastrado ainda.</p>
        <?php else: ?>

        <table class="tabela-dados">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Cargo</th>
                    <th>Registo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilizadores as $utilizador): ?>
                <tr>
                    <td><?= htmlspecialchars($utilizador['id_utilizador']) ?></td>
                    <td><?= htmlspecialchars($utilizador['nome_utilizador']) ?></td>
                    <td><?= htmlspecialchars($utilizador['password_hash']) ?></td>
                    <td><?= htmlspecialchars($utilizador['nome_role']) ?></td>
                    <td><?= date('d/m/Y', strtotime($utilizador['data_criacao'])) ?></td>
                    <td class="acoes">
                        <a href="editar_utilizador.php?id=<?= $utilizador['id_utilizador'] ?>" title="Editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="apagar_utilizador.php?id=<?= $utilizador['id_utilizador'] ?>" class="btn-apagar"
                            title="Apagar"
                            onclick="return confirm('Tem certeza que deseja apagar o utilizador <?= htmlspecialchars($utilizador['nome_utilizador']) ?>? Esta ação é irreversível.');">
                            <i class="fas fa-trash-alt"></i> Apagar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>

        <p style="margin-top: 20px;">
            <a href=" dashboard.php">Voltar ao Dashboard</a>
        </p>
        <p style=" margin-top: 20px;">
            <a href="cadastrar_utilizadores.php">Voltar ao Cadastro de Utilizadores</a>
        </p>
    </div>
</body>

</html>