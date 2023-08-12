<?php
// require_once '../config/sentence.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <p>Ejemplo de libreria para graficar</p>
    <div class="chart-container">
        <canvas id="sharedSqlChart"></canvas>
        <canvas id="databaseBufferChart"></canvas>
        <canvas id="redoLogBufferChart"></canvas>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var sharedSqlChartCtx = document.getElementById('sharedSqlChart').getContext('2d');
            var sharedSqlChart = new Chart(sharedSqlChartCtx, {
                type: 'bar',
                data: {
                    labels: ['SQL1', 'SQL2', 'SQL3', 'SQL4', 'SQL5'],
                    datasets: [{
                        label: 'Tiempo de Ejecución (segundos)',
                        data: [10, 15, 5, 8, 12],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

</body>

</html>