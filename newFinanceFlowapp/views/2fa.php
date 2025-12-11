<?php
require_once __DIR__ . '/../includes/config.php';

// Garante que o usuário esteja logado
if (!isset($_SESSION['id']) || !isset($_SESSION['2fa_pending'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Busca o e-mail e telefone do usuário
$stmt = $conn->prepare("SELECT email, phone_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$email = $result['email'];
$phone = $result['phone_number'];
$stmt->close();

// Envio do novo código 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_code'])) {
    $code = rand(100000, 999999);
    $expires_at = date('Y-m-d H:i:s', time() + 60);
    $hash = password_hash($code, PASSWORD_DEFAULT);

    $stmt_del = $conn->prepare("DELETE FROM two_factor_codes WHERE user_id = ?");
    $stmt_del->bind_param("i", $user_id);
    $stmt_del->execute();
    $stmt_del->close();

    $stmt = $conn->prepare("INSERT INTO two_factor_codes (user_id, code_hash, contact_method, expires_at) VALUES (?, ?, ?, ?)");
    $method = $_POST['method'] ?? 'email';
    $stmt->bind_param("isss", $user_id, $hash, $method, $expires_at);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Código enviado: $code');</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA - FinanceFlow</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../public/css/2fa.css">
</head>

<body>

<div class="container">

    <h2>Verificação em Duas Etapas</h2>

    <p>Selecione como deseja receber o código:</p>

    <form method="POST">
        <select name="method" class="input-field">
            <option value="email">E-mail (<?php echo htmlspecialchars($email); ?>)</option>
            <option value="phone">Celular (<?php echo htmlspecialchars($phone); ?>)</option>
        </select>

        <button type="submit" name="send_code" class="btn">Enviar Código</button>
    </form>

    <hr class="divider">

    <form action="verify_2fa.php" method="POST" class="verify-form">
        <label for="code">Digite o código recebido:</label>

        <input type="text" name="code" id="code" maxlength="6" required class="input-field">

        <p id="timer">
            O código expira em: <span id="countdown">01:00</span>
        </p>

        <button type="submit" class="btn">Verificar</button>
    </form>

    <div class="accessibility">
        <button onclick="toggleDarkMode()">🌙</button>
        <button onclick="changeFontSize(1)">A+</button>
        <button onclick="changeFontSize(-1)">A-</button>
    </div>

</div>

<!-- JS com DEFER (ESSENCIAL) -->
<script src="../public/js/2fa.js" defer></script>

</body>
</html>
