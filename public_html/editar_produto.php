<?php
// Arquivo: public_html/editar_produto.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// Proteção: Garante que só administradores logados podem aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// ---------------------------------------------
// 1. OBTER ID DO PRODUTO E BUSCAR DADOS
// ---------------------------------------------

// Verifica se o ID do produto foi passado na URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    // Se o ID for inválido ou estiver ausente, redireciona com mensagem de erro.
    $_SESSION['mensagem_erro'] = "ID de produto inválido ou não fornecido.";
    header("Location: gerir_produtos.php");
    exit();
}

$id_produto = $_GET['id'];
$produto = null;

try {
    $pdo = getConnection();
    
    // Prepara a consulta para evitar injeção de SQL
    $sql = "SELECT id_produto, nome_produto, codigo_barras, preco_compra, preco_venda, stock 
            FROM produtos 
            WHERE id_produto = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_produto, PDO::PARAM_INT);
    $stmt->execute();
    
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se o produto não for encontrado, redireciona
    if (!$produto) {
        $_SESSION['mensagem_erro'] = "Produto com ID $id_produto não encontrado.";
        header("Location: gerir_produtos.php");
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['mensagem_erro'] = "Erro de base de dados ao carregar produto: " . $e->getMessage();
    error_log("Erro em editar_produto.php: " . $e->getMessage());
    header("Location: gerir_produtos.php");
    exit();
}

// O resto do código continua para exibir o formulário...

?>

<!DOCTYPE html>
<html lang="pt-ao">

<head>
    <meta charset="UTF-8">
    <title>Editar Produto - Painel Admin</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .container {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: #333;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-group button {
        background-color: #27ae60;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #27ae27;
    }

    .btn-cancelar {
        background-color: #6c757d;
        padding: 10px 15px;
        margin-left: 10px;
        border-radius: 4px;
    }

    .btn-cancelar:hover {
        background-color: #5a6268;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-edit"></i> Editar Produto: <?= htmlspecialchars($produto['nome_produto']) ?></h1>

        <form action="processa_edicao_produto.php" method="POST">

            <input type="hidden" name="id_produto" value="<?= htmlspecialchars($produto['id_produto']) ?>">

            <div class="form-group">
                <label for="nome_produto">Nome do Produto:</label>
                <input type="text" id="nome_produto" name="nome_produto"
                    value="<?= htmlspecialchars($produto['nome_produto']) ?>" required>
            </div>

            <div class="form-group">
                <label for="codigo_barras">Código de Barras (opcional):</label>
                <input type="text" id="codigo_barras" name="codigo_barras"
                    value="<?= htmlspecialchars($produto['codigo_barras'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="preco_compra">Preço de Custo (Kz):</label>
                <input type="number" step="0.01" id="preco_compra" name="preco_compra"
                    value="<?= htmlspecialchars($produto['preco_compra']) ?>" required>
            </div>

            <div class="form-group">
                <label for="preco_venda">Preço de Venda (Kz):</label>
                <input type="number" step="0.01" id="preco_venda" name="preco_venda"
                    value="<?= htmlspecialchars($produto['preco_venda']) ?>" required>
            </div>

            <div class="form-group">
                <label for="stock">Stock Atual:</label>
                <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($produto['stock']) ?>"
                    required>
            </div>

            <div class="form-group">
                <button type="submit">Salvar Alterações</button>
                <a href="gerir_produtos.php" class="form-group button btn-cancelar"
                    style="text-decoration: none;">Cancelar</a>
            </div>
        </form>
    </div>
</body>

</html>