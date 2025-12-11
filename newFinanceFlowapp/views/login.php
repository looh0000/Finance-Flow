<?php
// Inclui o arquivo de configuração, que inicia a sessão e a conexão com o BD
require_once __DIR__ . '/../includes/config.php';

$email = $password = "";
$email_err = $password_err = $login_err = "";
$success_msg = ""; 

// Mensagem de sucesso do cadastro
if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $success_msg = "Cadastro realizado com sucesso! Faça seu login.";
}

// Processa o formulário de login
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validação básica
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor, insira o seu e-mail.";
    } else{
        $email = trim($_POST["email"]);
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor, insira a sua senha.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Se não houver erros, busca no banco
    if(empty($email_err) && empty($password_err)){

        // AGORA o role foi adicionado aqui ↓↓↓
        $sql = "SELECT id, full_name, email, password_hash, is_2fa_enabled, role 
                FROM users WHERE email = ?";

        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            if($stmt->execute()){
                $stmt->store_result();

                if($stmt->num_rows == 1){

                    // AGORA o role foi adicionado no bind ↓↓↓
                    $stmt->bind_result($id, $full_name, $email, $hashed_password, $is_2fa_enabled, $role);

                    if($stmt->fetch()){

                        // Verifica senha
                        if(password_verify($password, $hashed_password)){

                            // Login OK — salva dados na sessão
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            $_SESSION["full_name"] = $full_name;

                            // SALVA O ROLE NA SESSÃO ↓↓↓ OBRIGATÓRIO
                            $_SESSION["role"] = $role;

                            // === LÓGICA 2FA ===
                            if ($is_2fa_enabled == 1) { 
                                $_SESSION["2fa_pending"] = true;
                                $_SESSION["2fa_verified"] = false;

                                header("location: 2fa.php");
                                exit;
                            } else {
                                $_SESSION["2fa_verified"] = true;
                                header("location: dashboard.php");
                                exit;
                            }

                        } else {
                            $login_err = "E-mail ou senha inválidos.";
                        }
                    }
                } else {
                    $login_err = "E-mail ou senha inválidos.";
                }
            } else {
                $login_err = "Oops! Algo deu errado. Tente novamente mais tarde.";
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
    <title>Login | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/login-styles.css">
    <link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
</head>
<body class="register-body">
    <div class="split-content-wrapper">
        <div class="info-side-panel">
            <img src="../public/assets/logofianceflow.png" alt="Logo" class="side-panel-logo">
            <h2>Bem-vindo de Volta!</h2>
            <div class="info-list"> 
                <div class="info-item">
                    <ion-icon name="trending-up-outline"></ion-icon>
                    <p>Continue monitorando o crescimento do seu patrimônio e suas metas.</p>
                </div>
                <div class="info-item">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <p>Login seguro e acesso imediato aos seus dados financeiros.</p>
                </div>
                <div class="info-item">
                    <ion-icon name="stats-chart-outline"></ion-icon>
                    <p>Acompanhe suas transações em tempo real.</p>
                </div>
            </div>
            <a href="register.php" class="btn-side-register">
                Não tem Login? Cadastre-se
            </a>
        </div>

        <div class="form-container">
            <div class="form-block">
                <img src="../public/assets/logofianceflow.png" alt="Logo" class="logo-interno">
                <h2>Acesse sua Conta</h2>

                <?php if (!empty($success_msg)): ?>
                    <div class="alert success-message"><?php echo $success_msg; ?></div>
                <?php endif; ?>

                <?php if (!empty($login_err)): ?>
                    <div class="alert error-message"><?php echo $login_err; ?></div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="form-input" required 
                               value="<?php echo htmlspecialchars($email); ?>">
                    </div>

                    <div class="input-group">
                        <label for="password">Senha</label>
                        <input type="password" name="password" id="password" class="form-input" required>
                    </div>

                    <div class="forgot-password">
                        <a href="reset-password.php">Esqueceu sua senha?</a>
                    </div>

                    <div class="form-actions">
                        <input type="submit" class="btn-login" value="Entrar">
                    </div>
                </form>
            </div>
        </div> 
    </div> 

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const body = document.body;
        const toggleBtn = document.createElement("button");
        toggleBtn.innerHTML = "🌙";
        toggleBtn.setAttribute("id", "dark-toggle");
        document.body.appendChild(toggleBtn);
        toggleBtn.style.position = "fixed";
        toggleBtn.style.top = "15px";
        toggleBtn.style.right = "15px";
        toggleBtn.style.zIndex = "1000";
        toggleBtn.style.padding = "10px 15px";
        toggleBtn.style.borderRadius = "50%";
        toggleBtn.style.border = "none";
        toggleBtn.style.cursor = "pointer";
        toggleBtn.style.background = "#333";
        toggleBtn.style.color = "#fff";
        toggleBtn.style.fontSize = "20px";

        toggleBtn.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            localStorage.setItem("darkMode", body.classList.contains("dark-mode"));
        });

        if (localStorage.getItem("darkMode") === "true") {
            body.classList.add("dark-mode");
        }
    });
    </script>
</body>
</html>
