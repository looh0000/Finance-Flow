/**
 * Script JavaScript para a página de Relatórios
 * Usa Chart.js para renderizar:
 * 1. Gráfico de Rosca (Despesas por Categoria)
 * 2. Gráfico de Linha (Evolução do Saldo Líquido)
 */

document.addEventListener('DOMContentLoaded', function() {
    // Variáveis passadas pelo PHP:
    // chart1Labels, chart1Values (Categoria)
    // chart2Labels, chart2Values (Evolução)

    // 1. Gráfico de Despesas por Categoria (Rosca)
    createCategoryExpensesChart(chart1Labels, chart1Values);

    // 2. Gráfico de Evolução do Saldo Líquido (Linha)
    createBalanceEvolutionChart(chart2Labels, chart2Values);
});

// FUNÇÃO PARA CRIAR O GRÁFICO DE DESPESAS POR CATEGORIA
function createCategoryExpensesChart(labels, values) {
    const ctx = document.getElementById('categoryExpensesChart');
    if (!ctx) return;

    const backgroundColors = [
        '#0077ff', // Azul (Alimentação)
        '#f44336', // Vermelho (Moradia)
        '#ffc107', // Amarelo (Transporte)
        '#10dc60', // Verde (Lazer)
        '#9c27b0'  // Roxo (Outros)
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
                    position: 'bottom',
                },
            }
        }
    });
}

// FUNÇÃO PARA CRIAR O GRÁFICO DE EVOLUÇÃO DO SALDO LÍQUIDO
function createBalanceEvolutionChart(labels, values) {
    const ctx = document.getElementById('balanceEvolutionChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Saldo Líquido Mensal (R$)',
                data: values,
                borderColor: '#0077ff',
                backgroundColor: 'rgba(0, 119, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Valor (R$)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
            }
        }
    });
}