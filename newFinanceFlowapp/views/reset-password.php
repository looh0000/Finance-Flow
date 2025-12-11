<?php 
require_once __DIR__ . '/../includes/config.php';

$email = ""; 
$message = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $error = "Por favor, insira seu e-mail.";
    } else {

        // 1. Verifica se o e-mail existe no banco
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {

                    // pega o ID do usuário
                    $stmt->bind_result($user_id);
                    $stmt->fetch();

                    // 2. Gerar token
                    $token = bin2hex(random_bytes(32));

                    // 3. Expira em 1 hora
                    $expires = date("Y-m-d H:i:s", time() + 3600);

                    // 4. Salvar no banco
                    $insert = $conn->prepare("
                        INSERT INTO password_resets (user_id, token, expires_at) 
                        VALUES (?, ?, ?)
                    ");
                    $insert->bind_param("iss", $user_id, $token, $expires);
                    $insert->execute();

                    // 5. Criar link (localhost)
                    $resetLink = "http://localhost/newFinanceFlowapp/views/new-password.php?token=" . $token;

                    // 6. Mostrar na tela
                    $message = "
                        Link de recuperação gerado com sucesso:<br><br>
                        <strong><a href='$resetLink' target='_blank'>$resetLink</a></strong>
                    ";

                } else {
                    $error = "Nenhum usuário encontrado com este e-mail.";
                }
            }
            $stmt->close();
        }
    }
    if ($conn) $conn->close();
}
?> 

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha | FinanceFlow</title>

    <!-- 🔥 AQUI O CSS FUNCIONA -->
    <link rel="stylesheet" href="../public/css/reset-password-styles.css?v=1">

    <link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
</head>

<body class="reset-password-body">

    <div class="central-form-wrapper">
        
        <div class="form-block">
            <h2>Recuperar Senha</h2>
            <p class="form-description">Insira seu e-mail para gerar um link de redefinição.</p>
            
            <?php if (!empty($message)): ?>
                <div class="alert success-message"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    
                <div class="input-group">
                    <label for="email">E-mail Cadastrado</label>
                    <input type="email" name="email" id="email" class="form-input" required 
                           value="<?php echo htmlspecialchars($email); ?>" 
                           placeholder="example@gmail.com">
                </div>

                <div class="form-actions">
                    <input type="submit" class="btn-submit" value="Gerar Link de Recuperação">
                </div>
            </form>

            <div class="login-link">
                <p><a href="login.php">← Voltar para o Login</a></p>
            </div>
        </div>
    </div>

</body>
</html>
