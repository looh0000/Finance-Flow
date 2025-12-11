<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';

// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];
$export_msg = "";

// Dados Simulados para Estatísticas (Baseado em reports.page.ts)
$despesas_mes = "R$ 7.000,00";
$receitas_mes = "R$ 9.500,00";
$balanco_mes = "+ R$ 2.500,00";

// 2. LÓGICA DE AÇÃO: EXPORTAR PDF (Simulação)
if (isset($_POST['action']) && $_POST['action'] == 'export_pdf') {
    // Aqui estaria a lógica para usar uma biblioteca PHP (como Dompdf) para gerar o PDF.
    $export_msg = "Relatório exportado com sucesso para PDF! (Simulação)";
}

// 3. PREPARAÇÃO DOS DADOS PARA O CHART.JS

// Dados para Gráfico 1: Despesas por Categoria
$gastos_por_categoria = [
    'Alimentação' => 3000.00,
    'Moradia' => 2500.00,
    'Transporte' => 1000.00,
    'Lazer' => 500.00,
];

$chart1_labels = json_encode(array_keys($gastos_por_categoria));
$chart1_values = json_encode(array_values($gastos_por_categoria));


// Dados para Gráfico 2: Evolução do Saldo Líquido (Linha)
$evolucao_saldo = [
    'Jan' => 1000,
    'Fev' => 1500,
    'Mar' => 1200,
    'Abr' => 2000,
    'Mai' => 2200,
    'Jun' => 2500,
];

$chart2_labels = json_encode(array_keys($evolucao_saldo));
$chart2_values = json_encode(array_values($evolucao_saldo));

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/reports-styles.css"> 
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
                <li><a href="reports.php" class="menu-item active"><ion-icon name="document-text-outline"></ion-icon> Relatórios</a></li>
                <li><a href="profile.php" class="menu-item"><ion-icon name="person-circle-outline"></ion-icon> Perfil</a></li>
                <li><a href="settings.php" class="menu-item"><ion-icon name="settings-outline"></ion-icon> Configurações</a></li>
                <li><a href="support.php" class="menu-item"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
                <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>

            </ul>
            
            <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

        <main class="main-area">
            
            <header class="desktop-header">
                <h1>Relatórios e Análises</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <?php if (!empty($export_msg)): ?>
                    <div class="alert success-message"><?php echo $export_msg; ?></div>
                <?php endif; ?>
                
                <section class="reports-filter-bar">
                    
                    <form action="reports.php" method="get" class="filter-form">
                        <label for="periodo">Selecione o Período:</label>
                        <select id="periodo" name="periodo">
                            <option value="mes">Mês Atual</option>
                            <option value="trimestre">Último Trimestre</option>
                            <option value="ano">Ano Corrente</option>
                        </select>
                        <button type="submit" class="btn-primary-filter"><ion-icon name="funnel-outline"></ion-icon> Filtrar</button>
                    </form>

                    <form action="reports.php" method="post">
                        <input type="hidden" name="action" value="export_pdf">
                        <button type="submit" class="btn-secondary-export"><ion-icon name="download-outline"></ion-icon> Exportar PDF</button>
                    </form>
                </section>

                <section class="reports-stats-grid">
                    <div class="stat-card expense">
                        <ion-icon name="trending-down-outline"></ion-icon>
                        <h3>Total de Despesas</h3>
                        <p class="stat-value"><?php echo $despesas_mes; ?></p>
                    </div>
                    <div class="stat-card income">
                        <ion-icon name="trending-up-outline"></ion-icon>
                        <h3>Total de Receitas</h3>
                        <p class="stat-value"><?php echo $receitas_mes; ?></p>
                    </div>
                    <div class="stat-card balance">
                        <ion-icon name="calculator-outline"></ion-icon>
                        <h3>Balanço Líquido</h3>
                        <p class="stat-value positive"><?php echo $balanco_mes; ?></p>
                    </div>
                </section>

                <section class="charts-main-grid">
                    
                    <div class="chart-card large-chart">
                        <h2>Evolução do Saldo Líquido (Últimos 6 Meses)</h2>
                        <div class="chart-container-large">
                            <canvas id="balanceEvolutionChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="chart-card small-chart">
                        <h2>Despesas por Categoria</h2>
                        <div class="chart-container-small">
                            <canvas id="categoryExpensesChart"></canvas>
                        </div>
                        <div class="chart-legend-info">
                             <p>Análise de Gastos - Foque em Alimentação e Moradia.</p>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>
    
    <script>
        // Dados para Despesas por Categoria (Gráfico de Rosca)
        const chart1Labels = <?php echo $chart1_labels; ?>;
        const chart1Values = <?php echo $chart1_values; ?>;

        // Dados para Evolução do Saldo Líquido (Gráfico de Linha)
        const chart2Labels = <?php echo $chart2_labels; ?>;
        const chart2Values = <?php echo $chart2_values; ?>;
    </script>
    
    <script src="../public/js/reports.js"></script>
    
</body>
</html>