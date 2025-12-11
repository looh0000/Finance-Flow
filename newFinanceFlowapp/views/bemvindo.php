<?php
// Inclui a configuração para iniciar a sessão (e a conexão com o BD)
require_once __DIR__ . '/../includes/config.php';

// Redireciona se o usuário já estiver logado
/*if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header('Location: dashboard.php');
    exit;
}*/

// Dados para o Gráfico de Simulação (Representa Crescimento/Estabilidade)
$chart_labels = json_encode(['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun']);
$chart_values = json_encode([100, 125, 90, 150, 180, 160]);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-Vindo | FinanceFlow - Controle sua Vida Financeira</title>
    <link rel="stylesheet" href="../public/css/bemvindo-styles.css"> 
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
</head>
<body class="bemvindo-body"> 

    <div class="landing-container">
        
        <header class="landing-header">
            <img src="../public/assets/logofianceflow.png" alt="Logo FinanceFlow" class="header-logo">

            <nav class="main-nav">
                <a href="#sobre" class="nav-link">Sobre</a>
                <a href="#recursos" class="nav-link">Recursos</a>
                <a href="#dicas" class="nav-link">Dicas</a>
                <a href="#contato" class="nav-link">Suporte</a>
            </nav>

            <!-- 🔹 Acessibilidade -->
            <div class="accessibility-tools">
                <button id="increase-font" title="Aumentar Fonte"><ion-icon name="add-outline"></ion-icon></button>
                <button id="decrease-font" title="Diminuir Fonte"><ion-icon name="remove-outline"></ion-icon></button>
                <button id="toggle-dark" title="Ativar/Desativar Dark Mode"><ion-icon name="moon-outline"></ion-icon></button>
                <button id="toggle-reader" title="Ativar/Desativar Leitor"><ion-icon name="volume-high-outline"></ion-icon></button>
            </div>

            <div class="auth-buttons">
                <a href="login.php" class="btn-login-header">Entrar</a>
                <a href="register.php" class="btn-register-header">Começar Agora!</a>
            </div>
        </header>


        <section id="sobre" class="main-hero-section">
            <div class="hero-text">
                <h1>Seu Futuro Financeiro Começa Aqui e Agora.</h1>
                <p>O FinanceFlow é a sua plataforma completa para visualizar, planejar e conquistar a estabilidade financeira. Transforme incertezas em controle com nossas ferramentas digitais.</p>
                
                <ul class="key-features">
                    <li><ion-icon name="analytics-outline"></ion-icon> Relatórios Visuais e Precisos</li>
                    <li><ion-icon name="ribbon-outline"></ion-icon> Metas Integradas ao Orçamento</li>
                    <li><ion-icon name="lock-closed-outline"></ion-icon> Segurança e Privacidade de Dados</li>
                </ul>
                <a href="register.php" class="btn-call-to-action">Crie sua Conta e Comece Grátis</a>
            </div>
            
            <div class="hero-image">
                <img src="../public/assets/tecnologia.png" alt="Dashboard do aplicativo FinanceFlow">
            </div>
        </section>
        
        <section id="recursos" class="value-proposition-section">
            <h2>Por Que Você Precisa do FinanceFlow?</h2>
            
            <div class="three-column-grid">
                <div class="value-card">
                    <img src="../public/assets/notas.jpg" alt="Organização de notas" class="card-image-small">
                    <h3>Organização sem Esforço</h3>
                    <p>Diga adeus às planilhas complexas. Cadastre receitas e despesas em segundos, e visualize o balanço de todas as suas contas em um só lugar.</p>
                </div>

                <div class="value-card">
                    <img src="../public/assets/finanças.png" alt="Análise de Gráficos" class="card-image-small">
                    <h3>Análise Profunda e Gráficos</h3>
                    <p>Entenda seus padrões de consumo. Nossos relatórios detalhados e gráficos interativos (Rosca, Linha, Barra) te dão o poder de tomar decisões mais inteligentes.</p>
                </div>
                
                <div class="value-card">
                    <img src="../public/assets/acoes.jpeg" alt="Tela de investimento em ações" class="card-image-small">
                    <h3>Foco no Crescimento</h3>
                    <p>Monitore seus investimentos e veja o quanto falta para alcançar suas metas. Use o Bot de IA para dicas personalizadas sobre alocação de ativos.</p>
                </div>
            </div>
        </section>

        <section id="dicas" class="dicas-section">
            <h2>3 Dicas para sua Estabilidade Financeira</h2>
            
            <div class="three-column-grid">
                
                <div class="dica-card">
                    <ion-icon name="wallet-outline" class="dica-icon"></ion-icon>
                    <h3>1. Priorize a Reserva</h3>
                    <p>Mantenha uma reserva de emergência equivalente a 6 meses do seu custo de vida. Isso garante tranquilidade em momentos inesperados, sem recorrer a dívidas caras.</p>
                    <div class="chart-small-container">
                        <canvas id="simulacaoChart"></canvas>
                    </div>
                </div>

                <div class="dica-card">
                    <ion-icon name="swap-horizontal-outline" class="dica-icon"></ion-icon>
                    <h3>2. Adote a Regra 50/30/20</h3>
                    <p>Divida sua receita em: 50% para despesas essenciais (moradia, comida), 30% para lazer/desejos e **20% para investir e pagar dívidas**.</p>
                    <p class="final-quote">FinanceFlow te ajuda a monitorar esses 20% com precisão.</p>
                </div>

                <div class="dica-card">
                    <ion-icon name="trending-up-outline" class="dica-icon"></ion-icon>
                    <h3>3. Invista Continuamente</h3>
                    <p>Mesmo pequenas quantias, quando investidas de forma consistente e com juros compostos, transformam seu futuro. Use nossa seção de Metas para automatizar este hábito.</p>
                    <img src="../public/assets/logofianceflow.png" alt="Crescimento" class="mini-logo-dica">
                </div>
            </div>
        </section>

        <footer id="contato" class="landing-footer">
            <div class="footer-links">
                <a href="support.php" class="footer-link">Ajuda e Suporte</a>
                <a href="settings.php" class="footer-link">Termos e Privacidade</a>
                <a href="#recursos" class="footer-link">Recursos do App</a>
                <a href="login.php" class="footer-link">Acesso para Clientes</a>
            </div>
            <p class="copyright-text">
                &copy; <?php echo date('Y'); ?> **FinanceFlow**. Todos os direitos reservados.
                <br>Controle Hoje, Conquiste Amanhã.
            </p>
        </footer>
    </div>

    <script>
        const simLabels = <?php echo $chart_labels; ?>;
        const simValues = <?php echo $chart_values; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            createSimulationChart(simLabels, simValues);
        });

        function createSimulationChart(labels, values) {
            const ctx = document.getElementById('simulacaoChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Evolução Simulada',
                        data: values,
                        borderColor: '#10dc60',
                        backgroundColor: 'rgba(16, 220, 96, 0.2)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { display: false },
                        x: { display: false }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
        }
    </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let fontSize = 100;
            const body = document.body;
            const readerBtn = document.getElementById("toggle-reader");
            let reading = false;
            let synth = window.speechSynthesis;

            // Aumentar Fonte
            document.getElementById("increase-font").addEventListener("click", () => {
                fontSize += 10;
                body.style.fontSize = fontSize + "%";
            });

            // Diminuir Fonte
            document.getElementById("decrease-font").addEventListener("click", () => {
                if (fontSize > 50) {
                    fontSize -= 10;
                    body.style.fontSize = fontSize + "%";
                }
            });

            // Dark Mode
            document.getElementById("toggle-dark").addEventListener("click", () => {
                body.classList.toggle("dark-mode");
            });

            // Leitor de Texto (Text-to-Speech)
            readerBtn.addEventListener("click", () => {
                if (!reading) {
                    const text = document.body.innerText;
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = "pt-BR";
                    synth.speak(utterance);
                    reading = true;

                    utterance.onend = () => {
                        reading = false;
                    };
                } else {
                    synth.cancel();
                    reading = false;
                }
            });
        });
    </script>

</body>
</html>