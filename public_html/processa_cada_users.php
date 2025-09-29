<?php
// Arquivo: public_html/processa_cadastro.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// 1. SEGURANÇA: Garante que só um Admin logado pode aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'] || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$nome_utilizador = trim($_POST['nome_utilizador'] ?? '');
$password        = $_POST['password'] ?? '';
$id_role         = $_POST['id_role'] ?? '';

// 2. VALIDAÇÃO DE DADOS BÁSICA
if (empty($nome_utilizador) || empty($password) || empty($id_role)) {
    $_SESSION['mensagem_erro'] = "Todos os campos devem ser preenchidos.";
    header("Location: cadastrar_utilizadores.php");
    exit();
}

try {
    $pdo = getConnection();

    // 3. VERIFICAÇÃO DE DUPLICIDADE (Protege contra a criação de users duplicados)
    $sql_check = "SELECT id_utilizador FROM utilizadores WHERE nome_utilizador = :nome";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([':nome' => $nome_utilizador]);

    if ($stmt_check->fetch()) {
        $_SESSION['mensagem_erro'] = "O nome de utilizador '$nome_utilizador' já existe. Escolha outro.";
        header("Location: cadastrar_utilizadores.php");
        exit();
    }

    // 4. CRIPTOGRAFIA (Gera a hash segura)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 5. INSERÇÃO DO NOVO UTILIZADOR
    $sql_insert = "INSERT INTO utilizadores (nome_utilizador, password_hash, id_role)
                   VALUES (:nome, :hash, :role)";

    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        ':nome' => $nome_utilizador,
        ':hash' => $password_hash,
        ':role' => $id_role
    ]);

    // 6. FEEDBACK DE SUCESSO
    $_SESSION['mensagem_sucesso'] = "Utilizador '$nome_utilizador' cadastrado com sucesso!";
    header("Location: cadastrar_utilizadores.php");
    exit();
} catch (PDOException $e) {
    // 7. ERRO DE BASE DE DADOS
    $_SESSION['mensagem_erro'] = "Erro interno ao cadastrar utilizador. Tente novamente.";
    error_log("Erro no processa_cadastro.php: " . $e->getMessage());
    header("Location: cadastrar_utilizadores.php");
    exit();
}