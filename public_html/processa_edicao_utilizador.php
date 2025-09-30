<?php
// Arquivo: public_html/processa_edicao_utilizador.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// Proteção
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'] || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// ---------------------------------------------
// 1. OBTENÇÃO E VALIDAÇÃO DOS DADOS
// ---------------------------------------------

$id_utilizador = filter_input(INPUT_POST, 'id_utilizador', FILTER_VALIDATE_INT);
$nome_utilizador = trim(filter_input(INPUT_POST, 'nome_utilizador', FILTER_SANITIZE_STRING));
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$id_role = filter_input(INPUT_POST, 'id_role', FILTER_VALIDATE_INT);
$password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

$erros = [];

if (!$id_utilizador || !$id_role) {
    $erros[] = "ID do utilizador ou Cargo inválido.";
}
if (empty($nome_utilizador)) {
    $erros[] = "O nome de utilizador é obrigatório.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = "E-mail inválido.";
}
// Validação da NOVA Palavra-Passe (se fornecida)
if (!empty($password) && strlen($password) < 6) {
    $erros[] = "A nova palavra-passe deve ter pelo menos 6 caracteres.";
}

if (!empty($erros)) {
    $_SESSION['mensagem_erro'] = implode("<br>", $erros);
    header("Location: editar_utilizador.php?id=$id_utilizador");
    exit();
}

// ---------------------------------------------
// 2. PREPARAÇÃO DA QUERY
// ---------------------------------------------

$sql_base = "UPDATE utilizadores SET
                nome_utilizador = :nome,
                email = :email,
                id_role = :id_role";
$parametros = [
    ':nome' => $nome_utilizador,
    ':email' => $email,
    ':id_role' => $id_role,
    ':id' => $id_utilizador
];

// Lógica Condicional: Se a palavra-passe foi preenchida, adiciona-a à query
if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $sql_base .= ", password_hash = :hash";
    $parametros[':hash'] = $password_hash;
}

$sql_final = $sql_base . " WHERE id_utilizador = :id";

// ---------------------------------------------
// 3. EXECUÇÃO
// ---------------------------------------------

try {
    $pdo = getConnection();
    $stmt = $pdo->prepare($sql_final);
    $execucao = $stmt->execute($parametros);

    if ($execucao) {
        $_SESSION['mensagem_sucesso'] = "Utilizador **$nome_utilizador** (ID: $id_utilizador) atualizado com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Nenhuma alteração foi feita, ou ocorreu um erro desconhecido.";
    }
} catch (PDOException $e) {
    // 23000 é geralmente a violação de 'UNIQUE' (e-mail ou username já existe)
    if ($e->getCode() == 23000) {
        $_SESSION['mensagem_erro'] = "Erro: Nome de utilizador ou E-mail já existe no sistema.";
    } else {
        $_SESSION['mensagem_erro'] = "Erro de base de dados ao atualizar: " . $e->getMessage();
    }
    error_log("Erro em processa_edicao_utilizador.php: " . $e->getMessage());
}

// Redireciona para a lista
header("Location: gerir_utilizadores.php");
exit();