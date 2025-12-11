<?php
// Inclui o arquivo de configuração, que garante que a função session_start() seja chamada
require_once __DIR__ . '/../includes/config.php';

// 1. Limpa todas as variáveis de sessão
$_SESSION = array();

// 2. Destrói a sessão
session_destroy();

// 3. Redireciona o usuário para a página de Login
header("location: login.php");
exit;
?>