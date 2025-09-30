<?php
// Arquivo: public_html/apagar_utilizador.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// Proteção
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

$id_utilizador = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_utilizador) {
    $_SESSION['mensagem_erro'] = "ID de utilizador inválido ou ausente para eliminação.";
    header("Location: gerir_utilizadores.php");
    exit();
}

// Impedir que o ADMIN que está logado apague a si mesmo
if ($id_utilizador == $_SESSION['utilizador_id']) {
    $_SESSION['mensagem_erro'] = "Não pode apagar a sua própria conta enquanto estiver logado.";
    header("Location: gerir_utilizadores.php");
    exit();
}

try {
    $pdo = getConnection();

    // DELETE seguro
    $sql = "DELETE FROM utilizadores WHERE id_utilizador = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_utilizador, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['mensagem_sucesso'] = "Utilizador (ID: $id_utilizador) foi **apagado** com sucesso.";
    } else {
        $_SESSION['mensagem_erro'] = "Utilizador com ID $id_utilizador não foi encontrado ou já foi apagado.";
    }
} catch (PDOException $e) {
    // Tratar violação de chave estrangeira
    if ($e->getCode() == 23000) {
        $_SESSION['mensagem_erro'] = "Erro: O utilizador não pode ser apagado porque tem registos associados (faturas, etc.).";
    } else {
        $_SESSION['mensagem_erro'] = "Erro de base de dados ao apagar o utilizador: " . $e->getMessage();
    }
    error_log("Erro em apagar_utilizador.php: " . $e->getMessage());
}

header("Location: gerir_utilizadores.php");
exit();