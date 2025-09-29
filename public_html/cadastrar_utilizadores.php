<?php
// Arquivo: public_html/cadastrar_utilizadores.php

// Inicia a sessão e conecta ao banco de dados
require_once '../inc/funcoes.php';
iniciarSessao();
$pdo = getConnection();

// Proteção da página: só administradores podem aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// -----------------------------------------------------------------------------
// Lógica para obter os cargos (roles) da base de dados é mesmo compilicoso yeah
// -----------------------------------------------------------------------------
$roles = [];
try {
    $sql = "SELECT id_role, nome_role FROM roles ORDER BY nome_role";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $roles = $stmt->fetchAll();
} catch (PDOException $e) {
    // Em caso de erro, a variável $roles ficará vazia
    error_log("Erro ao buscar cargos da BD: " . $e->getMessage());
}

// Mensagens de sucesso ou erro (se existirem)
$mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? '';
$mensagem_erro    = $_SESSION['mensagem_erro'] ?? '';
unset($_SESSION['mensagem_sucesso'], $_SESSION['mensagem_erro']);

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Utilizadores - Gestão de Supermercado</title>
    <link rel="stylesheet" href="css/style_of_cada_users.css">
</head>

<body>
    <div class="container">
        <h1>Cadastrar Novo Utilizador</h1>

        <?php if ($mensagem_sucesso): ?>
        <p class="sucesso"><?= htmlspecialchars($mensagem_sucesso) ?></p>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
        <p class="erro"><?= htmlspecialchars($mensagem_erro) ?></p>
        <?php endif; ?>

        <form action="processa_cada_users.php" method="post">
            <label for="nome_utilizador">Nome de Utilizador:</label>
            <input type="text" id="nome_utilizador" name="nome_utilizador" required>

            <label for="password">Palavra-passe:</label>
            <input type="password" id="password" name="password" required>

            <label for="id_role">Cargo:</label>
            <select id="id_role" name="id_role" required>
                <?php foreach ($roles as $role): ?>
                <option value="<?= htmlspecialchars($role['id_role']) ?>">
                    <?= htmlspecialchars($role['nome_role']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Cadastrar</button>
        </form>
        <p><a href="dashboard.php">Voltar ao Painel</a></p>
    </div>
</body>

</html>