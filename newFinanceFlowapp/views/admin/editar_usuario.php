<?php
require_once __DIR__ . '/../../includes/config.php';


// Verifica ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: lista_usuarios.php?erro=ID_invalido");
    exit;
}

$id = intval($id);

// Buscar usuário
$sql = $conn->prepare("SELECT * FROM users WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$user = $sql->get_result()->fetch_assoc();

if (!$user) {
    header("Location: lista_usuarios.php?erro=usuario_nao_encontrado");
    exit;
}

// SALVAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['full_name'];
    $email = $_POST['email'];
    $telefone = $_POST['phone_number'];
    $status = $_POST['status'];
    $role = $_POST['role'];

    // Proteção: admin não pode remover próprio cargo
    if ($id == $_SESSION['user_id'] && $role !== "admin") {
        header("Location: editar_usuario.php?id=$id&erro=nao_pode_rebaixar");
        exit;
    }

    $up = $conn->prepare("
        UPDATE users 
        SET full_name=?, email=?, phone_number=?, status=?, role=?
        WHERE id=?
    ");

    $up->bind_param("sssssi", $nome, $email, $telefone, $status, $role, $id);
    $up->execute();

    header("Location: lista_usuarios.php?editado=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Usuário</title>

<style>
    body {
        background: #eef3f8;
        font-family: Arial;
        padding: 30px;
    }

    .card {
        width: 420px;
        margin: auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 {
        margin-bottom: 20px;
        text-align: center;
        color: #013574;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    button {
        background: #013574;
        color: #fff;
        padding: 12px;
        width: 100%;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }

    .btn-back {
        display: block;
        margin-top: 10px;
        text-align: center;
        color: #013574;
        font-weight: bold;
        text-decoration: none;
    }
</style>

</head>

<body>



<div class="card">
    <h2>Editar Usuário</h2>

    <form method="POST">

        <label>Nome completo</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

        <label>Telefone</label>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">

        <label>Status</label>
        <select name="status">
            <option value="ativo"     <?= $user['status']=="ativo" ? "selected" : "" ?>>Ativo</option>
            <option value="inativo"   <?= $user['status']=="inativo" ? "selected" : "" ?>>Inativo</option>
            <option value="bloqueado" <?= $user['status']=="bloqueado" ? "selected" : "" ?>>Bloqueado</option>
        </select>

        <label>Perfil</label>
        <select name="role">
            <option value="usuario" <?= $user['role']=="usuario" ? "selected" : "" ?>>Usuário</option>
            <option value="admin"   <?= $user['role']=="admin" ? "selected" : "" ?>>Admin</option>
        </select>

        <button type="submit">Salvar Alterações</button>

        <a href="lista_usuarios.php" class="btn-back">← Voltar ao Gerenciamento</a>

    </form>
</div>

</body>
</html>
