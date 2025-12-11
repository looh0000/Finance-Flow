/**
 * Script JavaScript para a página de Investimentos
 * Usa Chart.js para renderizar o gráfico de distribuição do portfólio.
 *
 * NOTA: As variáveis portfolioLabels e portfolioValues são definidas pelo PHP
 * no final do arquivo investimentos.php antes de este script ser carregado.
 */

document.addEventListener('DOMContentLoaded', function() {
    // A função é chamada após o carregamento da página para garantir que o canvas exista
    createPortfolioChart(portfolioLabels, portfolioValues);
});

function createPortfolioChart(labels, values) {
    const ctx = document.getElementById('portfolioChart');
    
    if (!ctx) {
        console.error("Canvas com ID 'portfolioChart' não encontrado.");
        return;
    }

    // Cores para o gráfico
    const backgroundColors = [
        '#115bda', // Azul principal
        '#10dc60', // Verde de rendimento
        '#ffc107', // Amarelo/Ouro
        '#f44336', // Vermelho 
        '#0c3394'  // Azul escuro
    ];

    new Chart(ctx, {
        type: 'doughnut', // Gráfico de Rosca/Pizza
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: backgroundColors.slice(0, labels.length),
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Permite que o chart-container-small defina o tamanho
            plugins: {
                legend: {
                    position: 'bottom', // Legenda na parte inferior
                },
                title: {
                    display: false
                }
            }
        }
    });
}