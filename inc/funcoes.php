<?php
// Arquivo: supermercado-faturacao/inc/funcoes.php

// O caminho 'config.php' é relativo à pasta 'inc/' onde este ficheiro está.
require_once 'config.php'; // <--- ESTE CAMINHO DEVE ESTAR CORRETO

/**
 * Inicia a sessão se ainda não estiver ativa.
 */
function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Cria e retorna uma nova conexão PDO à base de dados.
 * @return PDO A instância de conexão.
 */
function getConnection() {
    // Usamos as constantes de config.php
    $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro de Conexão com a Base de Dados: " . $e->getMessage());
        // Se houver erro de BD, paramos tudo e mostramos uma mensagem genérica
        exit("Erro fatal: Não foi possível conectar à base de dados. Verifique o inc/config.php.");
    }
}
?>