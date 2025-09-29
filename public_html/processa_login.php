<?php
// // Arquivo: supermercado-faturacao/public_html/processa_login.php

// require_once '../inc/funcoes.php';
// require_once '../inc/config.php'; // Inclui a constante ID_ADMINISTRADOR
// iniciarSessao();

// // Garante que o acesso é via POST
// if ($_SERVER["REQUEST_METHOD"] != "POST") {
//     header("Location: index.php");
//     exit();
// }

// $nome_utilizador = trim($_POST['nome_utilizador'] ?? '');
// $password        = $_POST['password'] ?? '';

// if (empty($nome_utilizador) || empty($password)) {
//     $_SESSION['erro_login'] = "Preencha o nome de utilizador e a palavra-passe.";
//     header("Location: index.php");
//     exit();
// }

// try {
//     $pdo = getConnection();

//     // 1. Consulta SQL usando JOIN para obter o nome do papel (role)
//     $sql = "SELECT
//                 u.id_utilizador,
//                 u.password_hash,
//                 u.id_role,
//                 r.nome_role
//             FROM utilizadores u
//             JOIN roles r ON u.id_role = r.id_role
//             WHERE u.nome_utilizador = :nome";

//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([':nome' => $nome_utilizador]);
//     $utilizador = $stmt->fetch();

//     // 2. Verificar o utilizador e a password
//     if ($utilizador && password_verify($password, $utilizador['password_hash'])) {
//         // LOGIN BEM-SUCEDIDO!

//         // 3. Iniciar as variáveis de sessão
//         $_SESSION['utilizador_id']    = $utilizador['id_utilizador'];
//         $_SESSION['nome_utilizador'] = $nome_utilizador;
//         $_SESSION['id_role']          = $utilizador['id_role'];
//         $_SESSION['nome_role']        = $utilizador['nome_role'];

//         // 4. Determinar se é Admin para facilitar as verificações
//         $_SESSION['is_admin']         = ($utilizador['id_role'] == ID_ADMINISTRADOR);

//         // 5. Redirecionar
//         header("Location: dashboard.php");
//         exit();
//     } else {
//         // 6. Login falhado
//         $_SESSION['erro_login'] = "Nome de utilizador ou Palavra-passe incorretos.";
//         header("Location: index.php");
//         exit();
//     }
// } catch (PDOException $e) {
//     // 7. Erro de Base de Dados
//     $_SESSION['erro_login'] = "Ocorreu um erro interno. Tente novamente.";
//     error_log("Erro no processa_login.php: " . $e->getMessage());
//     header("Location: index.php");
//     exit();
// }

// Arquivo: supermercado-faturacao/public_html/processa_login.php

require_once '../inc/funcoes.php';
// Nota: O funcoes.php já inclui o config.php. Incluir o config.php aqui 
// não deve causar erro, mas é redundante. Mantemos para evitar problemas de path.
require_once '../inc/config.php';
iniciarSessao();

// Garante que o acesso é via POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$nome_utilizador = trim($_POST['nome_utilizador'] ?? '');
$password        = $_POST['password'] ?? '';

if (empty($nome_utilizador) || empty($password)) {
    $_SESSION['erro_login'] = "Preencha o nome de utilizador e a palavra-passe.";
    header("Location: index.php");
    exit();
}

try {
    $pdo = getConnection();

    // 1. Consulta SQL usando JOIN para obter o nome do papel (role)
    $sql = "SELECT
                u.id_utilizador,
                u.password_hash,
                u.id_role,
                r.nome_role
            FROM utilizadores u
            JOIN roles r ON u.id_role = r.id_role
            WHERE u.nome_utilizador = :nome";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nome' => $nome_utilizador]);
    $utilizador = $stmt->fetch();

    // 2. Verificar o utilizador e a password (Versão SEGURA)
    if ($utilizador && password_verify($password, $utilizador['password_hash'])) {
        // LOGIN BEM-SUCEDIDO!
        #### ------------------------------------------------------------------------------------
        // ... (restante código de sessão e redirecionamento) ...

        // 2. VERIFICAÇÃO TEMPORÁRIA: APENAS VERIFICA SE O UTILIZADOR EXISTE.
        // IGNORA a validação da password para testar o fluxo de redirecionamento.
        // if ($utilizador) { // <-- A ALTERAÇÃO ESTÁ AQUI!

        // LOGIN BEM-SUCEDIDO! (APENAS DIAGNÓSTICO)
        #### ------------------------------------------------------------------------------------
        // 3. Iniciar as variáveis de sessão
        $_SESSION['utilizador_id']    = $utilizador['id_utilizador'];
        $_SESSION['nome_utilizador'] = $nome_utilizador;
        $_SESSION['id_role']          = $utilizador['id_role'];
        $_SESSION['nome_role']        = $utilizador['nome_role'];

        // 4. Determinar se é Admin
        $_SESSION['is_admin']         = ($utilizador['id_role'] == ID_ADMINISTRADOR);

        // 5. Redirecionar
        header("Location: dashboard.php");
        exit();
    } else {
        // 6. Login falhado (Nome de utilizador não encontrado)
        $_SESSION['erro_login'] = "Nome de utilizador ou Palavra-passe incorretos.";
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    // 7. Erro de Base de Dados
    $_SESSION['erro_login'] = "Ocorreu um erro interno. Tente novamente.";
    error_log("Erro no processa_login.php: " . $e->getMessage());
    header("Location: index.php");
    exit();
}