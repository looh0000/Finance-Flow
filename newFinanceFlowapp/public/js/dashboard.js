/**
 * Lógica JavaScript para a Dashboard (Substitui dashboard.page.ts)
 * * Este script inicializa o Chart.js para mostrar o gráfico de comparação anual.
 */

document.addEventListener('DOMContentLoaded', function() {
    // A função é executada quando o DOM está completamente carregado
    createAnnualComparisonChart();
});

function createAnnualComparisonChart() {
    const ctx = document.getElementById('annualComparisonChart');
    
    // Verifica se o elemento canvas existe na página antes de tentar criar o gráfico
    if (!ctx) {
        console.error("Canvas com ID 'annualComparisonChart' não encontrado.");
        return;
    }
    
    // Dados baseados em dashboard.page.ts
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [
                {
                    label: 'Receitas',
                    data: [4500, 4200, 4700, 4900, 5100, 5200, 5300, 5400, 5600, 5700, 5900, 6100],
                    backgroundColor: 'rgba(75, 192, 192, 0.8)', // Cor de Receitas
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Despesas',
                    data: [2000, 2100, 1800, 1900, 2200, 2300, 2500, 2400, 2600, 2700, 2900, 3100],
                    backgroundColor: 'rgba(255, 99, 132, 0.8)', // Cor de Despesas
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Permite que o CSS controle a altura (chart-container)
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}