<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            max-width: 350px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        h2 {
            margin-bottom: 10px;
            color: #d9534f;
        }
        p {
            margin-bottom: 20px;
            color: #555;
        }
        a {
            background: #115bda;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
        }
        a:hover {
            background: #0e4ebb;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>🚫 Acesso Negado</h2>
    <p>Você não tem permissão para acessar esta página.</p>
    <a href="/newFinanceFlowapp/views/dashboard.php">Voltar ao Dashboard</a>
</div>

</body>
</html>
