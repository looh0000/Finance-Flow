<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';

// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];

// Lógica de ações para simular o clique nos botões (usando JavaScript e mailto:)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'open_faq') {
        echo "<script>alert('Abrindo a FAQ... (Simulação)');</script>";
    }
    
    // O envio de e-mail é feito pelo link mailto no HTML, mas podemos simular a ação.
    if ($_GET['action'] == 'open_social') {
        echo "<script>alert('Abrindo redes sociais... (Simulação)');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/support-styles.css"> 
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
                <li><a href="support.php" class="menu-item active"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
                <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>

            </ul>
            
            <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

        <main class="main-area">
            
            <header class="desktop-header">
                <h1>Suporte e Ajuda</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <div class="support-card">
                    <p class="support-intro">Aqui você pode encontrar respostas rápidas ou entrar em contato com nossa equipe de suporte.</p>
                    
                    <ul class="support-list">
                        <li class="support-item">
                            <label>FAQ</label>
                            <a href="support.php?action=open_faq" class="btn-support">Ver</a>
                        </li>

                        <li class="support-item">
                            <label>Contato por E-mail</label>
                            <a href="mailto:suporte@financeflow.com?subject=Solicitação de Suporte" class="btn-support btn-email">Enviar</a>
                        </li>

                        <li class="support-item">
                            <label>Redes Sociais</label>
                            <a href="support.php?action=open_social" class="btn-support">Ver</a>
                        </li>
                    </ul>

                    <div class="contact-details">
                        <h4>Outros Contatos</h4>
                        <p><strong>Telefone:</strong> (99) 9999-9999</p>
                        <p><strong>Chat Online:</strong> Disponível 24/7 (Simulação)</p>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>