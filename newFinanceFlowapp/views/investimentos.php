<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';


// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];

// Dados Simulados de Investimentos
$valor_total_investido = "R$ 25.000,00";
$rendimento_mes = "+ R$ 250,50";
$porcentagem_rendimento = "+ 1.01%";

$portifolio = [
    ['ativo' => 'Fundo Imobiliário', 'tipo' => 'Renda Variável', 'valor' => 12000.00, 'rent' => '8.5%'],
    ['ativo' => 'CDB Banco Azul', 'tipo' => 'Renda Fixa', 'valor' => 10000.00, 'rent' => '105% CDI'],
    ['ativo' => 'Ações Tech', 'tipo' => 'Ações', 'valor' => 3000.00, 'rent' => '3.2%'],
];

// 2. PREPARAÇÃO DOS DADOS PARA O CHART.JS
$chart_data_php = [
    'Fundo Imobiliário' => 12000.00,
    'CDB Banco Azul' => 10000.00,
    'Ações Tech' => 3000.00,
];

// Converte os rótulos e valores para JSON
$chart_labels = json_encode(array_keys($chart_data_php));
$chart_values = json_encode(array_values($chart_data_php));

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investimentos | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/investimentos-styles.css"> 
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
                <li><a href="investimentos.php" class="menu-item active"><ion-icon name="cash-outline"></ion-icon> Investimentos</a></li>
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
                <h1>Meus Investimentos</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <section class="overview-grid">
                    <div class="total-invested-card">
                        <h3>Valor Total Investido</h3>
                        <p class="big-value"><?php echo $valor_total_investido; ?></p>
                    </div>
                    <div class="performance-card">
                        <h3>Rendimento Mensal</h3>
                        <p class="performance-value <?php echo (strpos($rendimento_mes, '+') !== false) ? 'positive' : 'negative'; ?>">
                            <?php echo $rendimento_mes; ?>
                        </p>
                        <p class="small-info"><?php echo $porcentagem_rendimento; ?> de Retorno</p>
                    </div>
                    <div class="action-shortcuts">
                        <a href="#" class="btn-invest">Novo Investimento</a>
                        <a href="#" class="btn-resgatar">Resgatar</a>
                    </div>
                </section>

                <section class="content-investment-grid">
                    
                    <div class="portfolio-card">
                        <h2>Meu Portfólio</h2>
                        <ul class="asset-list">
                            <?php foreach ($portifolio as $ativo): ?>
                            <li class="asset-item">
                                <div class="asset-details">
                                    <h4 class="asset-name"><?php echo htmlspecialchars($ativo['ativo']); ?></h4>
                                    <p class="asset-type"><?php echo htmlspecialchars($ativo['tipo']); ?></p>
                                </div>
                                <div class="asset-stats">
                                    <span class="asset-value">R$ <?php echo number_format($ativo['valor'], 2, ',', '.'); ?></span>
                                    <span class="asset-rent"><?php echo htmlspecialchars($ativo['rent']); ?></span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="side-widgets">
                        
                        <div class="widget-card">
                            <h2>Distribuição (%)</h2>
                            <div class="chart-container-small">
                                <canvas id="portfolioChart"></canvas>
                            </div>
                        </div>

                        <div class="widget-card investment-tips">
                            <h2>Dicas de Ouro</h2>
                            <ul>
                                <li>Diversifique seu portfólio para reduzir riscos.</li>
                                <li>Reinvista os dividendos para alavancar os ganhos.</li>
                                <li>Mantenha a reserva de emergência em liquidez diária.</li>
                            </ul>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>
    
    <script>
        // Dados PHP convertidos para variáveis JavaScript
        const portfolioLabels = <?php echo $chart_labels; ?>;
        const portfolioValues = <?php echo $chart_values; ?>;
    </script>
    
    <script src="../public/js/investimentos.js"></script>
    
</body>
</html>