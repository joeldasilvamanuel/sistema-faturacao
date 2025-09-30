<?php
// Arquivo: public_html/cadastrar_produtos.php

require_once '../inc/funcoes.php';
iniciarSessao();

// Proteção: Garante que só administradores logados podem aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Mensagens de sucesso ou erro (se existirem)
$mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? '';
$mensagem_erro    = $_SESSION['mensagem_erro'] ?? '';
unset($_SESSION['mensagem_sucesso'], $_SESSION['mensagem_erro']);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produtos - Gestão de Supermercado</title>
    <link rel="stylesheet" href="css/style_of_cada_produtos.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container">
        <h1>Cadastrar Novo Produto</h1>

        <?php if ($mensagem_sucesso): ?>
        <p class="sucesso"><?= htmlspecialchars($mensagem_sucesso) ?></p>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
        <p class="erro"><?= htmlspecialchars($mensagem_erro) ?></p>
        <?php endif; ?>

        <form action="processa_produtos.php" method="post">

            <label for="nome_produto">Nome do Produto:</label>
            <input type="text" id="nome_produto" name="nome_produto" required>

            <label for="codigo_barras">Código de Barras (Opcional):</label>
            <input type="text" id="codigo_barras" name="codigo_barras">

            <label for="preco_compra">Preço de Custo (KZ):</label>
            <input type="number" id="preco_compra" name="preco_compra" step="0.01" min="0" value="0.00" required>

            <label for="preco_venda">Preço de Venda (KZ):</label>
            <input type="number" id="preco_venda" name="preco_venda" step="0.01" min="0" required>

            <label for="stock">Stock Inicial (Quantidade):</label>
            <input type="number" id="stock" name="stock" min="0" required>

            <button type="submit">Cadastrar Produto</button>
        </form>
        <p><a href="dashboard.php">Voltar ao Painel</a></p>
    </div>

    <!-- Botão flutuante Gerir Produtos -->
    <a href="gerir_produtos.php" class="btn-float btn-products" title="Gerir Produtos">
        <i class="fas fa-warehouse"></i>
    </a>

    <style>
    .btn-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        transition: 0.3s ease;
        z-index: 9999;
    }

    .btn-float:hover {
        transform: scale(1.1);
    }

    .btn-users {
        background: #007bff;
        bottom: 80px;
        /* fica acima do botão de produtos */
    }

    .btn-products {
        background: #28a745;
    }
    </style>

</body>

</html>