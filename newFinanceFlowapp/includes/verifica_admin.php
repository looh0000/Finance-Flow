<?php
session_start();

// Caminho base relativo à raiz do projeto
define('BASE_URL', '/newFinanceFlowapp/views/');

// --- VERIFICA LOGIN ---
if (!isset($_SESSION['id']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../views/login.php"); // ajuste o caminho se necessário
    exit();
}


// --- VERIFICA PERMISSÃO DE ADMIN ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redireciona para página de acesso negado
    header("Location: " . BASE_URL . "admin/acesso_negado.php");
    exit();
}
?>
