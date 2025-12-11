<?php
require_once __DIR__ . '/../includes/config.php'; // Conexão com o banco

// Inicializa variáveis de mensagem
$message = "";
$error = "";

// Captura o token da URL
$token = $_GET['token'] ?? "";

if (empty($token)) {
    die("Token inválido.");
}

// Verifica se o token existe e não expirou na tabela password_resets
$stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Token inválido ou expirado.");
}

$resetData = $result->fetch_assoc();
$user_id = $resetData['user_id'];
$expires_at = strtotime($resetData['expires_at']);

if ($expires_at < time()) {
    die("O token expirou. Solicite uma nova recuperação de senha.");
}

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (strlen($password) < 6) {
        $error = "A senha precisa ter pelo menos 6 caracteres.";
    } else {
        // Atualiza a senha no banco usando password_hash
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $updateStmt->bind_param("si", $hashedPassword, $user_id);

        if ($updateStmt->execute()) {
            // Remove o token usado da tabela password_resets
            $delStmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delStmt->bind_param("s", $token);
            $delStmt->execute();

            // Redireciona para login após sucesso
            header("Location: login.php?reset=success");
            exit; // importante
        } else {
            $error = "Erro ao atualizar a senha. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Senha</title>
    <link rel="stylesheet" href="../public/css/new-password-styles.css?v=1">
</head>
<body>

<div class="central-box">

    <h2>Definir Nova Senha</h2>
    <p class="description">Digite sua nova senha para concluir o processo.</p>

    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <label style="float: left; margin-bottom: 5px; font-weight: 600;">Nova senha</label>
        <input type="password" name="password" required placeholder="Digite a nova senha">

        <button type="submit">Salvar nova senha</button>
    </form>

    <div class="back-login">
        <a href="login.php">← Voltar ao login</a>
    </div>

</div>

</body>
</html>
