<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';


// 1. VERIFICAÇÃO DE SESSÃO E VARIÁVEIS INICIAIS
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$full_name = $email = $dateOfBirth = $gender = $phone_number = $date_created = ""; 
$profile_err = $profile_success = "";
$password_err = $password_success = "";

// 2. BUSCAR DADOS ATUAIS DO USUÁRIO
$sql_select = "SELECT full_name, email, date_of_birth, gender, phone_number, created_at FROM users WHERE id = ?";
if ($stmt_select = $conn->prepare($sql_select)) {
    $stmt_select->bind_param("i", $user_id);
    if ($stmt_select->execute()) {
        $stmt_select->bind_result($full_name, $email, $dateOfBirth, $gender, $phone_number, $date_created);
        $stmt_select->fetch();
        $_SESSION["full_name"] = $full_name;
    }
    $stmt_select->close();
} else {
    $profile_err = "Erro ao buscar dados do perfil. Tente novamente mais tarde.";
}

// 3. LÓGICA PARA ATUALIZAR NOME, EMAIL E CELULAR
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $new_full_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    $new_dateOfBirth = trim($_POST['dateOfBirth']);
    $new_gender = trim($_POST['gender']);
    $new_phone_number = trim($_POST['phone_number']);
    
    if (empty($new_full_name) || empty($new_email)) {
        $profile_err = "Nome e Email não podem ser vazios.";
    } 

    if (empty($profile_err)) {
        $sql_update = "UPDATE users SET full_name = ?, email = ?, date_of_birth = ?, gender = ?, phone_number = ? WHERE id = ?";
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("sssssi", $new_full_name, $new_email, $new_dateOfBirth, $new_gender, $new_phone_number, $user_id);
            
            if ($stmt_update->execute()) {
                $profile_success = "Perfil atualizado com sucesso!";
                $full_name = $new_full_name;
                $email = $new_email;
                $dateOfBirth = $new_dateOfBirth;
                $gender = $new_gender;
                $phone_number = $new_phone_number;
                $_SESSION["full_name"] = $full_name;
            } else {
                if ($conn->errno == 1062) { 
                    $profile_err = "O email fornecido já está em uso.";
                } else {
                    $profile_err = "Erro ao atualizar perfil. Tente novamente: " . $conn->error;
                }
            }
            $stmt_update->close();
        }
    }
}

// 4. LÓGICA PARA ATUALIZAR SENHA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_password') {
    $sql_pass = "SELECT password_hash FROM users WHERE id = ?";
    $hash_password = "";
    if ($stmt_pass = $conn->prepare($sql_pass)) {
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $stmt_pass->bind_result($hash_password);
        $stmt_pass->fetch();
        $stmt_pass->close();
    }
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($hash_password) || !password_verify($current_password, $hash_password)) {
        $password_err = "A senha atual está incorreta.";
    } elseif (empty($new_password) || strlen($new_password) < 6) {
        $password_err = "A nova senha deve ter pelo menos 6 caracteres.";
    } elseif ($new_password !== $confirm_password) {
        $password_err = "A confirmação da nova senha não confere.";
    }

    if (empty($password_err)) {
        $new_hash_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update_pass = "UPDATE users SET password_hash = ? WHERE id = ?"; 
        
        if ($stmt_update_pass = $conn->prepare($sql_update_pass)) {
            $stmt_update_pass->bind_param("si", $new_hash_password, $user_id);
            if ($stmt_update_pass->execute()) {
                $password_success = "Senha atualizada com sucesso!";
            } else {
                $password_err = "Erro ao atualizar senha: " . $conn->error;
            }
            $stmt_update_pass->close();
        }
    }
}

$conn->close();
$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];

?>

<!DOCTYPE html>
<html lang="pt-br"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/profile-styles.css"> 
    <link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="web-layout-container">

        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="../public/assets/logofianceflow.png" alt="Logo" class="sidebar-logo">
                <h3>FinanceFlow</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="menu-item"><ion-icon name="stats-chart-outline"></ion-icon> Dashboard</a></li>
                <li><a href="accounts.php" class="menu-item"><ion-icon name="wallet-outline"></ion-icon> Contas</a></li>
                <li><a href="metas.php" class="menu-item"><ion-icon name="ribbon-outline"></ion-icon> Metas</a></li>
                <li><a href="transacoes.php" class="menu-item"><ion-icon name="swap-horizontal-outline"></ion-icon> Transações</a></li>
                <li><a href="investimentos.php" class="menu-item"><ion-icon name="cash-outline"></ion-icon> Investimentos</a></li>
                <li><a href="reports.php" class="menu-item"><ion-icon name="document-text-outline"></ion-icon> Relatórios</a></li>
                <li><a href="profile.php" class="menu-item active"><ion-icon name="person-circle-outline"></ion-icon> Perfil</a></li>
                <li><a href="settings.php" class="menu-item"><ion-icon name="settings-outline"></ion-icon> Configurações</a></li>
                <li><a href="support.php" class="menu-item"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
                <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>

            </ul>
            
            <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

        <main class="main-area">
            
            <header class="desktop-header">
                <h1>Meu Perfil</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <div class="profile-info-column">
                    <div class="profile-card profile-details-card">
                        <div class="profile-picture-section">
                            <ion-icon name="person-circle" class="profile-avatar"></ion-icon>
                            <h2><?php echo htmlspecialchars($full_name); ?></h2>
                        </div>
                        
                        <div class="detail-group">
                            <h4>Detalhes da Conta</h4>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                            <p><strong>Celular:</strong> <?php echo htmlspecialchars($phone_number); ?></p>
                            <p><strong>Nascimento:</strong> <?php echo date('d/m/Y', strtotime($dateOfBirth)); ?></p>
                            <p><strong>Gênero:</strong> <?php echo htmlspecialchars($gender); ?></p>
                            <p><strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($date_created)); ?></p>
                        </div>
                    </div>
                </div>

                <div class="profile-form-column">
                    
                    <div class="profile-card">
                        <h2>Atualizar Dados Pessoais</h2>
                        
                        <?php if (!empty($profile_err)): ?>
                            <div class="alert error-message"><?php echo $profile_err; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($profile_success)): ?>
                            <div class="alert success-message"><?php echo $profile_success; ?></div>
                        <?php endif; ?> 

                        <form action="profile.php" method="post" class="profile-form">
                            <input type="hidden" name="action" value="update_profile">

                            <label for="full_name">Nome Completo</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>

                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                            <label for="phone_number">Telefone</label>
                            <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" placeholder="(99) 99999-9999">

                            <label for="dateOfBirth">Data de Nascimento</label>
                            <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($dateOfBirth); ?>" required>

                            <label for="gender">Gênero</label>
                            <select id="gender" name="gender" required>
                                <option value="feminino" <?php echo ($gender == 'feminino' ? 'selected' : ''); ?>>Feminino</option>
                                <option value="masculino" <?php echo ($gender == 'masculino' ? 'selected' : ''); ?>>Masculino</option>
                                <option value="outros" <?php echo ($gender == 'outros' ? 'selected' : ''); ?>>Outros</option>
                            </select>

                            <button type="submit" class="btn-save-profile">Salvar Alterações</button>
                        </form>
                    </div>

                </div>

            </div>
        </main>
    </div>
</body>
</html>