/**
 * Script JavaScript para a página de Metas
 * Renderiza um gráfico de pizza (Doughnut) para mostrar a distribuição dos valores das metas.
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Verifica se as variáveis globais (passadas pelo PHP) existem
    if (typeof chartLabelsMetas === 'undefined' || typeof chartValuesMetas === 'undefined') {
        console.error("Dados do gráfico (chartLabelsMetas ou chartValuesMetas) não foram definidos pelo PHP.");
        return;
    }
    
    // 2. Chama a função de criação do gráfico
    createGoalDistributionChart(chartLabelsMetas, chartValuesMetas);
});

function createGoalDistributionChart(labels, values) {
    const ctx = document.getElementById('goalDistributionChart');
    if (!ctx) {
        console.error("Elemento canvas com ID 'goalDistributionChart' não encontrado no DOM.");
        return;
    }

    // Cores para Curto Prazo e Longo Prazo
    const backgroundColors = [
        '#0077ff', // Curto Prazo (Azul)
        '#10dc60', // Longo Prazo (Verde)
    ];

    new Chart(ctx, {
        type: 'doughnut',
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
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right', // Coloca a legenda à direita para caber no widget
                },
                title: {
                    display: false
                }
            }
        }
    });
}