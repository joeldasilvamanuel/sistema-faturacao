<?php
// Arquivo: public_html/editar_utilizador.php

require_once '../inc/funcoes.php';
require_once '../inc/config.php';
iniciarSessao();

// Proteção: Garante que só administradores logados podem aceder
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

$id_utilizador = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_utilizador) {
    $_SESSION['mensagem_erro'] = "ID de utilizador inválido ou ausente.";
    header("Location: gerir_utilizadores.php");
    exit();
}

$utilizador = null;
$roles = [];

try {
    $pdo = getConnection();

    // 1. Buscar dados do utilizador (inclui 'email' e 'data_cadastro' que precisam existir na DB)
    $sql_user = "SELECT id_utilizador, nome_utilizador, id_role
                 FROM utilizadores
                 WHERE id_utilizador = :id";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->bindParam(':id', $id_utilizador, PDO::PARAM_INT);
    $stmt_user->execute();

    $utilizador = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$utilizador) {
        $_SESSION['mensagem_erro'] = "Utilizador com ID $id_utilizador não encontrado.";
        header("Location: gerir_utilizadores.php");
        exit();
    }

    // 2. Buscar todos os Roles (Cargos) disponíveis
    $sql_roles = "SELECT id_role, nome_role FROM roles ORDER BY nome_role";
    $stmt_roles = $pdo->query($sql_roles);
    $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['mensagem_erro'] = "Erro de base de dados ao carregar dados: " . $e->getMessage();
    error_log("Erro em editar_utilizador.php: " . $e->getMessage());
    header("Location: gerir_utilizadores.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-ao">

<head>
    <meta charset="UTF-8">
    <title>Editar Utilizador: <?= htmlspecialchars($utilizador['nome_utilizador']) ?></title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .container {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: #333;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-group button {
        background-color: #27ae60;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #27ae27;
    }

    .btn-cancelar {
        background-color: #6c757d;
        padding: 10px 15px;
        margin-left: 10px;
        border-radius: 4px;
    }

    .btn-cancelar:hover {
        background-color: #5a6268;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-user-edit"></i> Editar Utilizador</h1>

        <form action="processa_edicao_utilizador.php" method="POST">

            <input type="hidden" name="id_utilizador" value="<?= htmlspecialchars($utilizador['id_utilizador']) ?>">

            <div class="form-group">
                <label for="nome_utilizador">Nome de Utilizador/Username:</label>
                <input type="text" id="nome_utilizador" name="nome_utilizador"
                    value="<?= htmlspecialchars($utilizador['nome_utilizador']) ?>" required>
            </div>

            <!-- <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($utilizador['email']) ?>"
                    required>
            </div> -->

            <div class="form-group">
                <label for="id_role">Cargo/Nível de Acesso:</label>
                <select id="id_role" name="id_role" required>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id_role'] ?>"
                        <?= ($role['id_role'] == $utilizador['id_role']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['nome_role']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <hr style="margin: 25px 0;">

            <div class="form-group">
                <label for="password">Nova Palavra-Passe (Deixe vazio para manter a atual):</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres">
                <p class="aviso-senha"><i class="fas fa-exclamation-triangle"></i> Deixe este campo em branco para **não
                    alterar** a palavra-passe atual.</p>
            </div>

            <div class="form-group">
                <button type="submit">Salvar Alterações</button>
                <a href="gerir_utilizadores.php" class="form-group button btn-cancelar"
                    style="text-decoration: none;">Cancelar</a>
                <a href="gerir_utilizadores.php" class="form-group button btn-cancelar" style="text-decoration: none;"
                    style="color: #27ae27;">Voltar</a>
            </div>
        </form>
    </div>
</body>

</html>