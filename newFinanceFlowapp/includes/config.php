<?php

// Inicia a sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Arquivo de Configuração do Banco de Dados MySQL (LOCALHOST)
 */

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'financialapp_php');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("ERRO: Não foi possível conectar ao banco de dados LOCAL. Detalhes: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// NÃO iniciar sessão aqui
// session_start();
?>
