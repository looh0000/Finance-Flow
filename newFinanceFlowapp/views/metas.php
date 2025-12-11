<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';


// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


$user_id = $_SESSION["id"];
$goal_err = $goal_success = "";

// 2. LÓGICA PARA ADICIONAR META (INSERT no MySQL)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_goal') {
    $nome = trim($_POST['nome']);
    $valor = floatval($_POST['valor']);
    $prazo = trim($_POST['prazo']);
    
    // Simulação: o valor inicial do progresso é 0
    $current_progress = 0.00; 

    if (!empty($nome) && $valor > 0 && !empty($prazo)) {
        $sql = "INSERT INTO goals (user_id, name, target_value, deadline_type, current_progress) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // isdsd -> Integer, String, Double, String, Double
            $stmt->bind_param("isdsd", $user_id, $nome, $valor, $prazo, $current_progress);
            
            if ($stmt->execute()) {
                $goal_success = "Meta '$nome' adicionada com sucesso!";
            } else {
                $goal_err = "Erro ao adicionar meta: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $goal_err = "Preencha todos os campos corretamente.";
    }
}

// 3. LÓGICA PARA REMOVER META (DELETE no MySQL)
if (isset($_GET['action']) && $_GET['action'] == 'remove_goal' && isset($_GET['id'])) {
    $goal_id = intval($_GET['id']);
    
    $sql = "DELETE FROM goals WHERE id = ? AND user_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $goal_id, $user_id);
        
        if ($stmt->execute()) {
            $goal_success = "Meta removida com sucesso!";
            header("location: metas.php?success=removed");
            exit;
        } else {
            $goal_err = "Erro ao remover meta: " . $conn->error;
        }
        $stmt->close();
    }
}

// 4. CARREGAR E PROCESSAR METAS (SELECT do MySQL)
$metas = [];
$total_goals = 0;
$goals_achieved = 0;
$total_target_value = 0;
$chart_data_php = ['Curto Prazo' => 0, 'Longo Prazo' => 0];

$sql_select = "SELECT id, name, target_value, deadline_type, current_progress FROM goals WHERE user_id = ?";

if ($stmt_select = $conn->prepare($sql_select)) {
    $stmt_select->bind_param("i", $user_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $total_goals++;
        $total_target_value += $row['target_value'];

        // Cálculo do progresso e status (para exibição)
        $progress_percent = ($row['target_value'] > 0) ? ($row['current_progress'] / $row['target_value']) * 100 : 0;
        $row['progress_percent'] = min(100, $progress_percent);
        
        if ($row['progress_percent'] >= 100) {
            $row['status'] = 'Concluída';
            $goals_achieved++;
        } elseif ($row['current_progress'] > 0) {
            $row['status'] = 'Em Progresso';
        } else {
            $row['status'] = 'Não Iniciada';
        }

        // Dados para o Gráfico (Curto vs. Longo Prazo)
        if ($row['deadline_type'] == 'mensal' || $row['deadline_type'] == 'semanal' || $row['deadline_type'] == 'diario') {
            $chart_data_php['Curto Prazo'] += $row['target_value'];
        } elseif ($row['deadline_type'] == 'anual') {
            $chart_data_php['Longo Prazo'] += $row['target_value'];
        }
        
        $metas[] = $row;
    }
    $stmt_select->close();
}

$conn->close();

// Prepara dados para JSON
$chart_labels = json_encode(array_keys($chart_data_php));
$chart_values = json_encode(array_values($chart_data_php));
$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/metas-styles.css"> 
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
                <li><a href="metas.php" class="menu-item active"><ion-icon name="ribbon-outline"></ion-icon> Metas</a></li>
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
                <h1>Metas Financeiras</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <?php if (!empty($goal_err)): ?>
                    <div class="alert error-message"><?php echo $goal_err; ?></div>
                <?php endif; ?>
                <?php if (!empty($goal_success) || (isset($_GET['success']) && $_GET['success'] == 'removed')): ?>
                    <div class="alert success-message"><?php echo $goal_success; ?></div>
                <?php endif; ?>
                
                <section class="meta-summary-grid">
                    
                    <div class="summary-goal-card total-value">
                        <h3>Valor Total em Metas</h3>
                        <p class="big-stat">R$ <?php echo number_format($total_target_value, 2, ',', '.'); ?></p>
                        <ion-icon name="ribbon-outline"></ion-icon>
                    </div>

                    <div class="summary-goal-card completed">
                        <h3>Metas Concluídas</h3>
                        <p class="big-stat"><?php echo $goals_achieved; ?> / <?php echo $total_goals; ?></p>
                        <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                    </div>
                    
                    <div class="chart-widget-goal">
                        <h2>Distribuição de Valores</h2>
                        <div class="chart-container-goal">
                            <canvas id="goalDistributionChart"></canvas>
                        </div>
                    </div>
                </section>


                <section class="main-meta-content">
                    
                    <div class="new-goal-card">
                        <h2>Adicionar Nova Meta</h2>
                        <form action="metas.php" method="post" class="goal-form">
                            <input type="hidden" name="action" value="add_goal">

                            <label for="nome">Nome da Meta</label>
                            <input type="text" id="nome" name="nome" required placeholder="Ex: Economizar para Emergência">

                            <label for="valor">Valor Alvo (R$)</label>
                            <input type="number" id="valor" name="valor" step="0.01" required placeholder="0.00">

                            <label for="prazo">Prazo (Frequência)</label>
                            <select id="prazo" name="prazo" required>
                                <option value="" disabled selected>Selecione o Prazo</option>
                                <option value="diario">Diário</option>
                                <option value="semanal">Semanal</option>
                                <option value="mensal">Mensal</option>
                                <option value="anual">Anual</option>
                            </select>

                            <button type="submit" class="btn-save-goal">Salvar Meta</button>
                        </form>
                    </div>

                    <div class="goals-list-section">
                        <h2>Minhas Metas Ativas</h2>
                        <?php if ($total_goals > 0): ?>
                            <ul class="goals-list">
                                <?php foreach ($metas as $meta): ?>
                                    <li class="goal-item meta-<?php echo strtolower(str_replace(' ', '-', $meta['status'])); ?>">
                                        <div class="goal-details">
                                            <h3><?php echo htmlspecialchars($meta['name']); ?></h3>
                                            <p>Alvo: R$ <?php echo number_format($meta['target_value'], 2, ',', '.'); ?> | Prazo: <?php echo htmlspecialchars($meta['deadline_type']); ?></p>
                                            
                                            <div class="progress-bar-container">
                                                <div class="progress-bar" style="width: <?php echo $meta['progress_percent']; ?>%;"></div>
                                            </div>
                                            
                                            <span class="progress-text"><?php echo number_format($meta['progress_percent'], 0, ',', '.'); ?>% (R$ <?php echo number_format($meta['current_progress'], 2, ',', '.'); ?>)</span>
                                        </div>
                                        
                                        <div class="goal-actions">
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $meta['status'])); ?>"><?php echo htmlspecialchars($meta['status']); ?></span>
                                            <a href="metas.php?action=remove_goal&id=<?php echo $meta['id']; ?>" class="btn-delete-goal" onclick="return confirm('Excluir esta meta?');">Excluir</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="no-goals">
                                <p>Nenhuma meta cadastrada. Use o formulário à esquerda para começar a planejar!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script>
        const chartLabelsMetas = <?php echo $chart_labels; ?>;
        const chartValuesMetas = <?php echo $chart_values; ?>;
    </script>
    <script src="../public/js/metas.js"></script>

</body>
</html>