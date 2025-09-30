<?php
// Arquivo: public_html/apagar_produto.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// Proteção: Garante que só administradores logados podem aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // Se não for admin, redireciona para a página principal
    header("Location: index.php");
    exit();
}

// ---------------------------------------------
// 1. OBTER E VALIDAR ID
// ---------------------------------------------

// Verifica se o ID do produto foi passado na URL e se é um número inteiro válido
$id_produto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_produto) {
    $_SESSION['mensagem_erro'] = "ID de produto inválido ou ausente para eliminação.";
    header("Location: gerir_produtos.php");
    exit();
}

// ---------------------------------------------
// 2. EXECUTAR A ELIMINAÇÃO (DELETE)
// ---------------------------------------------

try {
    $pdo = getConnection();

    // SQL: Apaga o registo na tabela 'produtos' com base no ID
    // Usamos um DELETE seguro via prepared statement
    $sql = "DELETE FROM produtos WHERE id_produto = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_produto, PDO::PARAM_INT);
    $stmt->execute();

    // Verifica se alguma linha foi afetada
    if ($stmt->rowCount() > 0) {
        $_SESSION['mensagem_sucesso'] = "Produto (ID: $id_produto) foi **apagado** com sucesso.";
    } else {
        $_SESSION['mensagem_erro'] = "Produto com ID $id_produto não foi encontrado ou já foi apagado.";
    }
} catch (PDOException $e) {
    // Em sistemas reais, devemos tratar casos de Chave Estrangeira (produtos em faturas)
    if ($e->getCode() == 23000) {
        $_SESSION['mensagem_erro'] = "Erro: O produto (ID: $id_produto) não pode ser apagado porque está ligado a registos de vendas (Chave Estrangeira).";
    } else {
        $_SESSION['mensagem_erro'] = "Erro de base de dados ao apagar o produto: " . $e->getMessage();
    }
    error_log("Erro em apagar_produto.php: " . $e->getMessage());
}

// Redireciona de volta para a lista de produtos
header("Location: gerir_produtos.php");
exit();