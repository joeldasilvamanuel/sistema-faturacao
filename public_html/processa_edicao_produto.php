<?php
// Arquivo: public_html/processa_edicao_produto.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// Proteção: Garante que só administradores logados podem aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'] || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// ---------------------------------------------
// 1. OBTENÇÃO E VALIDAÇÃO DOS DADOS DO FORMULÁRIO
// ---------------------------------------------

$id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);
$nome_produto = trim(filter_input(INPUT_POST, 'nome_produto', FILTER_SANITIZE_STRING));
$codigo_barras = trim(filter_input(INPUT_POST, 'codigo_barras', FILTER_SANITIZE_STRING));
$preco_compra = filter_input(INPUT_POST, 'preco_compra', FILTER_VALIDATE_FLOAT);
$preco_venda = filter_input(INPUT_POST, 'preco_venda', FILTER_VALIDATE_FLOAT);
$stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

// Variável para armazenar erros
$erros = [];

if (!$id_produto) {
    $erros[] = "ID do produto inválido ou ausente.";
}
if (empty($nome_produto)) {
    $erros[] = "O nome do produto é obrigatório.";
}
if ($preco_compra === false || $preco_compra < 0) {
    $erros[] = "Preço de Custo inválido.";
}
if ($preco_venda === false || $preco_venda < 0) {
    $erros[] = "Preço de Venda inválido.";
}
if ($stock === false || $stock < 0) {
    $erros[] = "Valor de Stock inválido.";
}

// ---------------------------------------------
// 2. PROCESSAMENTO (UPDATE) OU ERRO
// ---------------------------------------------

if (!empty($erros)) {
    // Se houver erros, armazena a mensagem e redireciona de volta para o formulário (melhoria futura: passar os erros para o formulário)
    $_SESSION['mensagem_erro'] = implode("<br>", $erros);
    // Redireciona para gerir_produtos ou, idealmente, para o formulário de edição
    header("Location: gerir_produtos.php");
    exit();
}

try {
    $pdo = getConnection();

    // -------------------------------------------------------------------
    // SQL: Atualiza o registo na tabela 'produtos' com base no ID
    // -------------------------------------------------------------------
    $sql = "UPDATE produtos SET
                nome_produto = :nome,
                codigo_barras = :cod_barras,
                preco_compra = :compra,
                preco_venda = :venda,
                stock = :stock
            WHERE id_produto = :id";

    $stmt = $pdo->prepare($sql);

    // Executa a query
    $execucao = $stmt->execute([
        ':nome' => $nome_produto,
        ':cod_barras' => $codigo_barras,
        ':compra' => $preco_compra,
        ':venda' => $preco_venda,
        ':stock' => $stock,
        ':id' => $id_produto
    ]);

    if ($execucao) {
        $_SESSION['mensagem_sucesso'] = "Produto **$nome_produto** (ID: $id_produto) atualizado com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Nenhuma alteração foi feita, ou ocorreu um erro desconhecido.";
    }
} catch (PDOException $e) {
    $_SESSION['mensagem_erro'] = "Erro de base de dados ao atualizar: " . $e->getMessage();
    error_log("Erro em processa_edicao_produto.php: " . $e->getMessage());
}

// Redireciona de volta para a lista de produtos após o processamento
header("Location: gerir_produtos.php");
exit();