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
</body>

</html>