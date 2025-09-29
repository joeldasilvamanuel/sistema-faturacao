<?php

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'db_supermercado');

// // Conexão
// function conectar_db()
// {
//     $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
//     if ($conn->connect_error) {
//         die("Conexão falhou: " . $conn->connect_error);
//     }
//     return $conn;
// }

// Arquivo: supermercado-faturacao/inc/config.php

// -----------------------------------------------------------------------------
// Configurações da Base de Dados (VERIFIQUE ESTES VALORES E A SINTAXE)
// -----------------------------------------------------------------------------
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'db_supermercado');

// ID do Role do Administrador (ID 5 é o Admin na sua BD)
define('ID_ADMINISTRADOR', 5);
?>