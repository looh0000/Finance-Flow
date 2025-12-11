<?php
// Inclui configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';


// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Variáveis de estado iniciais (Simulando defaults de settings.page.ts)
$notificationsEnabled = 1; // true
$darkModeEnabled = 0;      // false
$selectedLanguage = 'pt';  // Português

$settings_success = $settings_err = "";

// 2. LÓGICA DE PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ação principal: Salvar Configurações
    if (isset($_POST['action']) && $_POST['action'] == 'save_settings') {
        // Coleta os novos valores do formulário (o checkbox só envia valor se estiver marcado)
        $notificationsEnabled = isset($_POST['notificationsEnabled']) ? 1 : 0;
        $darkModeEnabled = isset($_POST['darkModeEnabled']) ? 1 : 0;
        $selectedLanguage = trim($_POST['selectedLanguage']);

        // Simulação de sucesso (Lógica real salvaria no DB)
        $settings_success = "Configurações salvas com sucesso!";
    }

    // Ação: Limpar Cache (simulando clearCache() de settings.page.ts)
    if (isset($_POST['action']) && $_POST['action'] == 'clear_cache') {
        echo "<script>alert('Cache limpo com sucesso!');</script>";
    }

    // Ação: Sobre o App (simulando showAbout() de settings.page.ts)
    if (isset($_POST['action']) && $_POST['action'] == 'show_about') {
        echo "<script>alert('Este aplicativo foi desenvolvido para fins educacionais.');</script>";
    }
}

$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/settings-styles.css"> 
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
                <li><a href="settings.php" class="menu-item active"><ion-icon name="settings-outline"></ion-icon> Configurações</a></li>
                <li><a href="support.php" class="menu-item"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
                <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>

            </ul>
            
            <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

        <main class="main-area">
            
            <header class="desktop-header">
                <h1>Configurações</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <div class="settings-card">
                    
                    <p class="settings-description">Aqui você pode ajustar suas preferências e configurações do aplicativo.</p>

                    <?php if (!empty($settings_success)): ?>
                        <div class="alert success-message"><?php echo $settings_success; ?></div>
                    <?php endif; ?>

                    <form action="settings.php" method="post" class="settings-form">
                        <input type="hidden" name="action" value="save_settings">

                        <div class="setting-item toggle-item">
                            <label for="notificationsEnabled">Notificações</label>
                            <input type="checkbox" id="notificationsEnabled" name="notificationsEnabled" 
                                class="toggle-switch" <?php echo ($notificationsEnabled ? 'checked' : ''); ?>>
                        </div>

                        <div class="setting-item toggle-item">
                            <label for="darkModeEnabled">Modo Escuro</label>
                            <input type="checkbox" id="darkModeEnabled" name="darkModeEnabled" 
                                class="toggle-switch" <?php echo ($darkModeEnabled ? 'checked' : ''); ?>>
                        </div>

                        <div class="setting-item">
                            <label for="selectedLanguage">Idioma</label>
                            <select id="selectedLanguage" name="selectedLanguage">
                                <option value="pt" <?php echo ($selectedLanguage == 'pt' ? 'selected' : ''); ?>>Português</option>
                                <option value="en" <?php echo ($selectedLanguage == 'en' ? 'selected' : ''); ?>>Inglês</option>
                                <option value="es" <?php echo ($selectedLanguage == 'es' ? 'selected' : ''); ?>>Espanhol</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-save-settings">Salvar Configurações</button>
                    </form>
                    
                    <div class="setting-actions">
                        <form action="settings.php" method="post">
                            <input type="hidden" name="action" value="clear_cache">
                            <div class="setting-item action-row">
                                <label>Limpar Cache</label>
                                <button type="submit" class="btn-action">Limpar</button>
                            </div>
                        </form>
                        
                        <form action="settings.php" method="post">
                            <input type="hidden" name="action" value="show_about">
                            <div class="setting-item action-row">
                                <label>Sobre o App</label>
                                <button type="submit" class="btn-action">Ver</button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </main>
    </div>
</body>
</html>