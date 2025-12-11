<?php
// Inclui config, session e conexão
require_once __DIR__ . '/../includes/config.php';

// --- BLOCO DE SEGURANÇA ---
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header("location: 2fa.php");
    exit;
}

// --- DEFINE SE É ADMIN ---
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// --- DADOS DO USUÁRIO ---
$nome_completo = $_SESSION["full_name"] ?? "Usuário";
$primeiro_nome = explode(' ', $nome_completo)[0];

// --- SIMULAÇÃO DE DADOS ---
$saldo_total = "R$ 10.000";
$receitas_total = "R$ 4.500";
$despesas_total = "R$ 2.000";
$lucro_geral = "R$ 2.500"; 
$receitas_mes = "R$ 1.500";
$despesas_mes = "R$ 800";

$recentTransactions = [
    ['description' => 'Compra em Supermercado', 'amount' => '-50.00', 'type' => 'expense', 'icon' => 'cart-outline'],
    ['description' => 'Salário Recebido', 'amount' => '+2000.00', 'type' => 'income', 'icon' => 'cash-outline'],
    ['description' => 'Pagamento de Conta', 'amount' => '-100.00', 'type' => 'expense', 'icon' => 'receipt-outline'],
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
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
            <li><a href="dashboard.php" class="menu-item active"><ion-icon name="stats-chart-outline"></ion-icon> Dashboard</a></li>
            <li><a href="accounts.php" class="menu-item"><ion-icon name="wallet-outline"></ion-icon> Contas</a></li>
            <li><a href="metas.php" class="menu-item"><ion-icon name="ribbon-outline"></ion-icon> Metas</a></li>
            <li><a href="transacoes.php" class="menu-item"><ion-icon name="swap-horizontal-outline"></ion-icon> Transações</a></li>
            <li><a href="investimentos.php" class="menu-item"><ion-icon name="cash-outline"></ion-icon> Investimentos</a></li>
            <li><a href="reports.php" class="menu-item"><ion-icon name="document-text-outline"></ion-icon> Relatórios</a></li>
            <li><a href="profile.php" class="menu-item"><ion-icon name="person-circle-outline"></ion-icon> Perfil</a></li>
            <li><a href="settings.php" class="menu-item"><ion-icon name="settings-outline"></ion-icon> Configurações</a></li>
            <li><a href="support.php" class="menu-item"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
            <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>

            <?php if ($isAdmin): ?>
                <li><a href="admin/lista_usuarios.php" class="menu-item"><ion-icon name="people-outline"></ion-icon> Usuários</a></li>
                <li><a href="admin/relatorios_admin.php" class="menu-item"><ion-icon name="analytics-outline"></ion-icon> Painel Admin</a></li>
            <?php endif; ?>
        </ul> 

        <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
    </nav>

    <main class="main-area">
        
        <header class="desktop-header">
            <h1>Dashboard</h1>
            <div class="user-widget">
                <p>
                    Olá, <?php echo $primeiro_nome; ?>!
                    <?php if ($isAdmin): ?>
                        <span style="color:red; font-weight:bold;">(Master)</span>
                    <?php endif; ?>
                </p>
                <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
            </div>
        </header>

        <div class="dashboard-content">

            <section class="summary-grid">
                <div class="summary-card total">
                    <h3>Saldo Total</h3>
                    <p class="value"><?php echo $saldo_total; ?></p>
                </div>

                <div class="summary-card income">
                    <h3>Receitas Mês</h3>
                    <p class="value"><?php echo $receitas_mes; ?></p>
                </div>

                <div class="summary-card expense">
                    <h3>Despesas Mês</h3>
                    <p class="value"><?php echo $despesas_mes; ?></p>
                </div>

                <div class="summary-card profit">
                    <h3>Lucro Geral</h3>
                    <p class="value"><?php echo $lucro_geral; ?></p> 
                </div>

                <?php if ($isAdmin): ?>
                <div class="summary-card admin">
                    <h3>Usuários Registrados</h3>
                    <p class="value">42</p>
                </div>
                <?php endif; ?>
            </section>
            
            <section class="main-content-grid">
                
                <div class="chart-area">
                    <h2>Comparação Anual</h2>
                    <p>Gráfico Comparativo de Receitas e Despesas</p>
                    <div class="chart-container">
                        <canvas id="annualComparisonChart"></canvas>
                    </div>
                </div>
                
                <div class="side-widgets">
                    
                    <div class="widget-card">
                        <h2>
                            <?php echo $isAdmin ? "Transações Recentes (Admin)" : "Suas Transações Recentes"; ?>
                        </h2>

                        <ul class="transaction-list">
                            <?php foreach ($recentTransactions as $t): ?>
                            <li class="transaction-item <?php echo $t['type']; ?>">
                                <ion-icon name="<?php echo $t['icon']; ?>" class="trans-icon"></ion-icon>
                                <span class="description"><?php echo $t['description']; ?></span>
                                <span class="amount"><?php echo $t['amount']; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="action-buttons">
                            <a href="transacoes.php" class="btn-full-width btn-primary">Ver Transações</a>
                            <a href="accounts.php" class="btn-full-width btn-secondary">Ver Contas</a>
                        </div>
                    </div>

                    <div class="widget-card alerts-card">
                        <h2>Alertas</h2>

                        <?php if ($isAdmin): ?>
                        <ul class="alert-list">
                            <li>📌 Novo usuário pendente de aprovação</li>
                            <li>📌 Acesso suspeito detectado</li>
                        </ul>
                        <?php else: ?>
                        <ul class="alert-list">
                            <li>⚠️ Saldo abaixo de R$ 1.000</li>
                            <li>⚠️ Meta mensal não atingida</li>
                        </ul>
                        <?php endif; ?>
                    </div>

                </div>
            </section>
        </div>
    </main>
</div>

<script src="../public/js/dashboard.js"></script>
</body>
</html>
