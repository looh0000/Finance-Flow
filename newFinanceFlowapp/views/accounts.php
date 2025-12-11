<?php
require_once __DIR__ . '/../includes/config.php';


// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];
$mensagem_sucesso = "";

// 2. ADICIONAR, EDITAR E EXCLUIR CONTAS
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ADICIONAR CONTA
    if (isset($_POST['adicionar_conta'])) {
        $nome_conta = trim($_POST['nome_conta']);
        $saldo_inicial = trim($_POST['saldo_inicial']);
        $tipo_conta = trim($_POST['tipo_conta']);

        if (!empty($nome_conta) && is_numeric($saldo_inicial)) {
            $stmt = $conn->prepare("INSERT INTO accounts (user_id, name, balance, type, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isds", $_SESSION['id'], $nome_conta, $saldo_inicial, $tipo_conta);
            $stmt->execute();
            $stmt->close();

            $mensagem_sucesso = "Conta '{$nome_conta}' adicionada com sucesso!";
        } else {
            $mensagem_sucesso = "Erro: Preencha todos os campos corretamente.";
        }
    }

    // EDITAR CONTA
    if (isset($_POST['editar_conta'])) {
        $id = $_POST['account_id'];
        $nome_conta = trim($_POST['nome_conta']);
        $tipo_conta = trim($_POST['tipo_conta']);

        if (!empty($nome_conta)) {
            $stmt = $conn->prepare("UPDATE accounts SET name=?, type=?, updated_at=NOW() WHERE id=? AND user_id=?");
            $stmt->bind_param("ssii", $nome_conta, $tipo_conta, $id, $_SESSION['id']);
            $stmt->execute();
            $stmt->close();

            $mensagem_sucesso = "Conta '{$nome_conta}' atualizada com sucesso!";
        } else {
            $mensagem_sucesso = "Erro: Nome da conta não pode estar vazio.";
        }
    }

    // EXCLUIR CONTA
    if (isset($_POST['excluir_conta'])) {
        $id = $_POST['account_id'];

        // Primeiro exclui as transações associadas (se quiser manter histórico, pode pular)
        $stmt = $conn->prepare("DELETE FROM transactions WHERE account_id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $_SESSION['id']);
        $stmt->execute();
        $stmt->close();

        // Exclui a conta
        $stmt = $conn->prepare("DELETE FROM accounts WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $_SESSION['id']);
        $stmt->execute();
        $stmt->close();

        $mensagem_sucesso = "Conta excluída com sucesso!";
    }
}

// 3. BUSCAR CONTAS DO USUÁRIO COM SALDO ATUALIZADO PELO MOVIMENTO
$sql = "SELECT a.id, a.name, a.type, a.balance,
        COALESCE((SELECT SUM(CASE WHEN t.type='entrada' THEN t.amount WHEN t.type='saida' THEN -t.amount END)
                  FROM transactions t
                  WHERE t.account_id = a.id),0) AS movimentacoes
        FROM accounts a
        WHERE a.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

$contas = [];
while($row = $result->fetch_assoc()){
    $row['saldo_atual'] = $row['balance'] + $row['movimentacoes'];
    $contas[] = $row;
}

// 4. CALCULO DO SALDO TOTAL
$saldo_total = array_sum(array_column($contas, 'saldo_atual'));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contas | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css">
    <link rel="stylesheet" href="../public/css/accounts-styles.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
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

            </ul>
            
            <a href="login.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

    <main class="main-area">
        <header class="desktop-header">
            <h1>Minhas Contas</h1>
            <div class="user-widget">
                <p>Olá, <?= htmlspecialchars($primeiro_nome); ?>!</p>
                <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
            </div>
        </header>

        <div class="page-content">
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert success-message"><?= $mensagem_sucesso; ?></div>
            <?php endif; ?>

            <section class="total-balance-card">
                <h2>Saldo Total Consolidado</h2>
                <p class="balance-value <?= $saldo_total >= 0 ? 'positive' : 'negative'; ?>">
                    R$ <?= number_format($saldo_total, 2, ',', '.'); ?>
                </p>
            </section>

            <!-- ADICIONAR CONTA -->
            <section class="add-account-section">
                <h2>Adicionar Nova Conta</h2>
                <form action="accounts.php" method="post" class="add-account-form">
                    <input type="hidden" name="adicionar_conta" value="1">
                    <div class="form-group">
                        <label for="nome_conta">Nome da Conta</label>
                        <input type="text" id="nome_conta" name="nome_conta" required>
                    </div>
                    <div class="form-group">
                        <label for="saldo_inicial">Saldo Inicial (R$)</label>
                        <input type="number" id="saldo_inicial" name="saldo_inicial" step="0.01" value="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="tipo_conta">Tipo de Conta</label>
                        <select id="tipo_conta" name="tipo_conta" required>
                            <option value="Banco Digital">Banco Digital</option>
                            <option value="Banco Tradicional">Banco Tradicional</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Investimentos">Investimentos</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-add-account">
                        <ion-icon name="add-circle-outline"></ion-icon> Adicionar Conta
                    </button>
                </form>
            </section>

            <!-- LISTA DE CONTAS -->
            <section class="account-list">
                <h2>Contas Registradas</h2>
                <?php if (empty($contas)): ?>
                    <p class="no-data-message">Nenhuma conta cadastrada.</p>
                <?php else: ?>
                    <div class="accounts-grid">
                        <?php foreach($contas as $conta):
                            $is_negative = $conta['saldo_atual'] < 0;
                        ?>
                            <div class="account-card">
                                <div class="account-header">
                                    <ion-icon name="wallet-outline"></ion-icon>
                                    <h3><?= htmlspecialchars($conta['name']); ?></h3>
                                </div>
                                <p class="account-type"><?= htmlspecialchars($conta['type']); ?></p>
                                <p class="account-balance">
                                    Saldo Atual:
                                    <span class="<?= $is_negative ? 'negative' : 'positive'; ?>">
                                        R$ <?= number_format($conta['saldo_atual'], 2, ',', '.'); ?>
                                    </span>
                                </p>

                                <!-- FORMULÁRIOS PARA EDITAR E EXCLUIR -->
                                <form action="accounts.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="account_id" value="<?= $conta['id']; ?>">
                                    <input type="hidden" name="editar_conta" value="1">
                                    <input type="text" name="nome_conta" value="<?= htmlspecialchars($conta['name']); ?>" required>
                                    <select name="tipo_conta" required>
                                        <option value="Banco Digital" <?= $conta['type']=='Banco Digital'?'selected':''; ?>>Banco Digital</option>
                                        <option value="Banco Tradicional" <?= $conta['type']=='Banco Tradicional'?'selected':''; ?>>Banco Tradicional</option>
                                        <option value="Cartão de Crédito" <?= $conta['type']=='Cartão de Crédito'?'selected':''; ?>>Cartão de Crédito</option>
                                        <option value="Dinheiro" <?= $conta['type']=='Dinheiro'?'selected':''; ?>>Dinheiro</option>
                                        <option value="Investimentos" <?= $conta['type']=='Investimentos'?'selected':''; ?>>Investimentos</option>
                                        <option value="Outro" <?= $conta['type']=='Outro'?'selected':''; ?>>Outro</option>
                                    </select>
                                    <button type="submit" class="btn-action edit">
                                        <ion-icon name="create-outline"></ion-icon> Editar
                                    </button>
                                </form>

                                <form action="accounts.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="account_id" value="<?= $conta['id']; ?>">
                                    <input type="hidden" name="excluir_conta" value="1">
                                    <button type="submit" class="btn-action delete"
                                            onclick="return confirm('Deseja realmente excluir esta conta?');">
                                        <ion-icon name="trash-outline"></ion-icon> Excluir
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </div>
    </main>
</div>
</body>
</html>
