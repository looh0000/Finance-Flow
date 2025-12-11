<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';


// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];

// Lógica para processar a mensagem do bot (Simulação)
$resposta_bot = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_message'])) {
    $message = trim($_POST['user_message']);
    
    if (empty($message)) {
        $resposta_bot = "Por favor, digite sua pergunta.";
    } else {
        // Simulação de resposta do bot (substituível por uma API de IA real)
        $resposta_bot = "Olá {$primeiro_nome}, entendi sua dúvida sobre '{$message}'. Eu posso te ajudar com dúvidas sobre saldo, orçamentos ou dicas de investimento.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinanceBot | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/bot-styles.css"> 
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
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
                <li><a href="profile.php" class="menu-item"><ion-icon name="person-circle-outline"></ion-icon> Perfil</a></li>
                <li><a href="settings.php" class="menu-item"><ion-icon name="settings-outline"></ion-icon> Configurações</a></li>
                <li><a href="support.php" class="menu-item"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
                <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>

            </ul>
            
            <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

        <main class="main-area">
            
            <header class="desktop-header">
                <h1>FinanceBot</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <div class="chat-container">
                    <div class="chat-history">
                        <div class="message bot-message">
                            <ion-icon name="happy-outline" class="avatar"></ion-icon>
                            <p>Olá! Eu sou o FinanceBot. Posso te ajudar a analisar seus dados financeiros ou responder a dúvidas gerais.</p>
                        </div>
                        
                        <?php if (!empty($resposta_bot)): ?>
                            <div class="message user-message">
                                <p><?php echo htmlspecialchars($message); ?></p>
                                <ion-icon name="person-outline" class="avatar"></ion-icon>
                            </div>
                            <div class="message bot-message">
                                <ion-icon name="happy-outline" class="avatar"></ion-icon>
                                <p><?php echo htmlspecialchars($resposta_bot); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form action="bot.php" method="post" class="chat-input-form">
                        <input type="text" name="user_message" placeholder="Pergunte algo ao FinanceBot..." required>
                        <button type="submit" class="btn-send">
                            <ion-icon name="send"></ion-icon>
                        </button>
                    </form>
                </div>

            </div>
        </main>
    </div>
</body>
</html>