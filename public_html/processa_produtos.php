<?php
// Arquivo: public_html/processa_produtos.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php'; // Para aceder à conexão PDO
iniciarSessao();

// 1. SEGURANÇA: Garante que só um Admin logado pode aceder via POST
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'] || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

// 2. Coletar e Limpar os Dados
$nome_produto   = trim($_POST['nome_produto'] ?? '');
$codigo_barras  = trim($_POST['codigo_barras'] ?? NULL); // Pode ser NULL
$preco_compra   = $_POST['preco_compra'] ?? 0;
$preco_venda    = $_POST['preco_venda'] ?? 0;
$stock          = $_POST['stock'] ?? 0;

// Converter valores para o formato numérico esperado (float)
$preco_compra_float = filter_var($preco_compra, FILTER_VALIDATE_FLOAT);
$preco_venda_float  = filter_var($preco_venda, FILTER_VALIDATE_FLOAT);
$stock_int          = filter_var($stock, FILTER_VALIDATE_INT);


// 3. VALIDAÇÃO DETALHADA
if (empty($nome_produto)) {
    $_SESSION['mensagem_erro'] = "O nome do produto é obrigatório.";
} elseif ($preco_venda_float === false || $preco_venda_float <= 0) {
    $_SESSION['mensagem_erro'] = "Preço de Venda inválido ou negativo. Deve ser um valor positivo.";
} elseif ($preco_compra_float === false || $preco_compra_float < 0) {
    $_SESSION['mensagem_erro'] = "Preço de Custo inválido. Use um valor numérico positivo.";
} elseif ($stock_int === false || $stock_int < 0) {
    $_SESSION['mensagem_erro'] = "O stock inicial deve ser um número inteiro positivo.";
}


// Se houver algum erro de validação, redireciona e para a execução
if (isset($_SESSION['mensagem_erro'])) {
    header("Location: cadastrar_produtos.php");
    exit();
}


try {
    $pdo = getConnection();

    // 4. VERIFICAÇÃO DE CÓDIGO DE BARRAS DUPLICADO (se fornecido)
    if (!empty($codigo_barras)) {
        $sql_check = "SELECT id_produto FROM produtos WHERE codigo_barras = :codigo";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([':codigo' => $codigo_barras]);

        if ($stmt_check->fetch()) {
            $_SESSION['mensagem_erro'] = "O código de barras '$codigo_barras' já está registado.";
            header("Location: cadastrar_produtos.php");
            exit();
        }
    }

    // 5. INSERÇÃO DO NOVO PRODUTO
    $sql_insert = "INSERT INTO produtos (nome_produto, codigo_barras, preco_compra, preco_venda, stock)
                   VALUES (:nome, :codigo, :compra, :venda, :stock)";

    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        ':nome'     => $nome_produto,
        ':codigo'   => empty($codigo_barras) ? NULL : $codigo_barras, // Garante que guarda NULL se estiver vazio
        ':compra'   => $preco_compra_float,
        ':venda'    => $preco_venda_float,
        ':stock'    => $stock_int
    ]);

    // 6. FEEDBACK DE SUCESSO
    $_SESSION['mensagem_sucesso'] = "Produto '$nome_produto' cadastrado com sucesso!";
    header("Location: cadastrar_produtos.php");
    exit();
} catch (PDOException $e) {
    // 7. ERRO DE BASE DE DADOS
    // Erros 23000 são tipicamente violações de unicidade (se o código de barras for inserido duas vezes sem ser verificado)
    if ($e->getCode() == '23000') {
        $_SESSION['mensagem_erro'] = "Erro: Já existe um produto com o mesmo código de barras.";
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao cadastrar produto. Tente novamente.";
        error_log("Erro no processa_produtos.php: " . $e->getMessage());
    }

    header("Location: cadastrar_produtos.php");
    exit();
}