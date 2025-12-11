<?php
require_once __DIR__ . '/../includes/config.php';

$registration_err = $registration_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullName = trim($_POST["fullName"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $dateOfBirth = trim($_POST["dateOfBirth"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $phone = trim($_POST["phone-number"] ?? "");
    $password = trim($_POST["password"]) ?? "";
    $confirmPassword = trim($_POST["confirmPassword"] ?? "");

    // Campos obrigatórios
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword) || empty($dateOfBirth) || empty($gender)) {
        $registration_err = "Por favor, preencha todos os campos obrigatórios.";
    }
    // Validação de e-mail (formato)
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_err = "E-mail inválido!";
    }
    // Confirmação de senha
    elseif ($password !== $confirmPassword) {
        $registration_err = "As senhas não coincidem!";
    }
    // Termos
    elseif (!isset($_POST["termsAccepted"])) {
        $registration_err = "Você precisa aceitar os termos e condições.";
    }
    // Validação de senha: 6 caracteres, letras e números
    elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6}$/', $password)) {
        $registration_err = "A senha deve ter 6 caracteres, contendo letras e números.";
    }
    else {
        // Verifica se e-mail já existe
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $registration_err = "Este e-mail já está em uso.";
                } else {
                    // Inserção no banco (coluna correta: phone_number)
                    $sql_insert = "INSERT INTO users (full_name, date_of_birth, gender, email, password_hash, phone_number) VALUES (?, ?, ?, ?, ?, ?)";
                    if ($stmt_insert = $conn->prepare($sql_insert)) {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt_insert->bind_param("ssssss", $fullName, $dateOfBirth, $gender, $email, $password_hash, $phone);
                        if ($stmt_insert->execute()) {
                            $registration_success = "Cadastro realizado com sucesso! Redirecionando para o login...";
                            header("refresh:3; url=login.php?success=registered");
                        } else {
                            $registration_err = "Erro ao criar a conta. Tente novamente mais tarde.";
                        }
                        $stmt_insert->close();
                    } else {
                        $registration_err = "Erro na preparação da consulta. Tente novamente mais tarde.";
                    }
                }
            } else {
                $registration_err = "Erro ao verificar e-mail. Tente novamente mais tarde.";
            }
            $stmt->close();
        } else {
            $registration_err = "Erro na preparação da verificação. Tente novamente mais tarde.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro - FinanceFlow</title>
<link rel="stylesheet" href="../public/css/register-styles.css">
<link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
</head>
<body class="register-body">

<button type="button" class="darkmode-toggle" id="darkmode-toggle" aria-label="Alternar Dark Mode">🌙</button>

<div class="split-content-wrapper">

    <div class="form-container">
        <h2>Crie sua Conta</h2>

        <?php if (!empty($registration_err)): ?>
            <div class="alert error-message"><?php echo $registration_err; ?></div>
        <?php endif; ?>
        <?php if (!empty($registration_success)): ?>
            <div class="alert success-message"><?php echo $registration_success; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="registration-form" novalidate>

            <div class="form-grid">

                <div class="form-group span-2">
                    <label for="fullName">Nome Completo</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Digite seu nome completo" required>
                </div>

                <div class="form-group">
                    <label for="dateOfBirth">Data de Nascimento</label>
                    <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gênero</label>
                    <select id="gender" name="gender" required>
                        <option value="" disabled selected>Selecione</option>
                        <option value="feminino">Feminino</option>
                        <option value="masculino">Masculino</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>

                <div class="form-group span-2">
                    <label for="phone-number">Telefone</label>
                    <input type="tel" id="phone-number" name="phone-number" placeholder="(99) 99999-9999" required>
                </div>

                <div class="form-group span-2">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
                </div>

                <div class="form-group password-group">
                    <label for="password">Senha (6 caracteres, letras e números, sem caracteres especiais.)</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Digite sua senha" required aria-describedby="password-message">
                        <ion-icon name="eye-outline" class="password-toggle" data-target="password" role="button" tabindex="0" aria-label="Mostrar senha"></ion-icon>
                    </div>
                    <small class="validation-message" id="password-message"></small>
                </div>

                <div class="form-group password-group">
                    <label for="confirmPassword">Confirmar Senha (As senhas devem ser iguais.)</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirme sua senha" required aria-describedby="confirmPassword-message">
                        <ion-icon name="eye-outline" class="password-toggle" data-target="confirmPassword" role="button" tabindex="0" aria-label="Mostrar confirmação de senha"></ion-icon>
                    </div>
                    <small class="validation-message" id="confirmPassword-message"></small>
                </div>

            </div>

            <div class="checkbox-container span-2">
                <input type="checkbox" id="termsAccepted" name="termsAccepted" value="1" required>
                <label for="termsAccepted">Eu concordo com os termos e condições</label>
            </div>

            <button type="submit" class="btn-register">Cadastrar-se</button>
            <p class="login-link">Já tem uma conta? <a href="login.php">Faça login</a></p>
        </form>
    </div>

    <div class="info-side-panel">
        <img src="../public/assets/logofianceflow.png" alt="Logo" class="side-panel-logo">
        <h2>Por Que se Cadastrar Agora?</h2>

        <div class="info-list">
            <div class="info-item">
                <ion-icon name="trending-up-outline"></ion-icon>
                <p>Comece a traçar suas metas de <strong>curto e longo prazo</strong>.</p>
            </div>
            <div class="info-item">
                <ion-icon name="shield-outline"></ion-icon>
                <p>Sua segurança é nossa prioridade.</p>
            </div>
            <div class="info-item">
                <ion-icon name="calculator-outline"></ion-icon>
                <p>Visão completa de todos os seus saldos e dívidas.</p>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // --- Dark Mode toggle ---
    const body = document.body;
    const darkBtn = document.getElementById('darkmode-toggle');

    function applyStoredDarkMode() {
        if (localStorage.getItem("darkMode") === "true") {
            body.classList.add("dark-mode");
            darkBtn.classList.add("dark");
            darkBtn.textContent = "☀️";
        } else {
            body.classList.remove("dark-mode");
            darkBtn.classList.remove("dark");
            darkBtn.textContent = "🌙";
        }
    }

    darkBtn.addEventListener("click", () => {
        const active = body.classList.toggle("dark-mode");
        localStorage.setItem("darkMode", active);
        applyStoredDarkMode();
    });

    applyStoredDarkMode();

    // --- Frontend validation: email, passwords ---
    document.querySelector('.registration-form').addEventListener('submit', function(e){
        const email = document.getElementById('email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('email').focus();
            alert('E-mail inválido! Verifique o formato.');
            e.preventDefault();
            return;
        }

        const pw = document.getElementById('password').value;
        const pwConfirm = document.getElementById('confirmPassword').value;
        if (pw !== pwConfirm) {
            document.getElementById('confirmPassword').focus();
            alert('As senhas não coincidem.');
            e.preventDefault();
            return;
        }

        const pwRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6}$/;
        if (!pwRegex.test(pw)) {
            document.getElementById('password').focus();
            alert('A senha deve ter 6 caracteres, contendo letras e números.');
            e.preventDefault();
            return;
        }
    });

    // --- Live password validation messages ---
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirmPassword');
    const passwordMsg = document.getElementById('password-message');
    const confirmMsg = document.getElementById('confirmPassword-message');

    passwordInput.addEventListener('input', () => {
        const regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6}$/;
        if (!regex.test(passwordInput.value)) {
            passwordMsg.textContent = "Senha inválida: use 6 caracteres, letras e números.";
        } else {
            passwordMsg.textContent = "";
        }
        if (confirmInput.value && passwordInput.value !== confirmInput.value) {
            confirmMsg.textContent = "As senhas não coincidem.";
        } else {
            confirmMsg.textContent = "";
        }
    });

    confirmInput.addEventListener('input', () => {
        if (confirmInput.value !== passwordInput.value) {
            confirmMsg.textContent = "As senhas não coincidem.";
        } else {
            confirmMsg.textContent = "";
        }
    });

    // --- Show/Hide password toggles ---
    document.querySelectorAll('.password-toggle').forEach(icon => {
        function toggleFor(targetId, elIcon) {
            const input = document.getElementById(targetId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                elIcon.setAttribute('name', 'eye-off-outline');
                elIcon.setAttribute('aria-label', 'Ocultar senha');
            } else {
                input.type = 'password';
                elIcon.setAttribute('name', 'eye-outline');
                elIcon.setAttribute('aria-label', 'Mostrar senha');
            }
            input.focus();
        }

        icon.addEventListener('click', (e) => {
            toggleFor(icon.getAttribute('data-target'), icon);
        });
        icon.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleFor(icon.getAttribute('data-target'), icon);
            }
        });
    });

    // --- Máscara e limitação para telefone ---
    const phoneInput = document.getElementById('phone-number');

    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // remove tudo que não for número
        if (value.length > 11) value = value.slice(0, 11);

        if (value.length > 6) {
            value = `(${value.slice(0,2)}) ${value.slice(2,7)}-${value.slice(7)}`;
        } else if (value.length > 2) {
            value = `(${value.slice(0,2)}) ${value.slice(2)}`;
        } else if (value.length > 0) {
            value = `(${value}`;
        }

        e.target.value = value;
    });

});
</script>

</body>
</html>
