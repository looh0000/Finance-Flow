<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/verifica_admin.php';  // garante que só admin pode excluir

// Verifica se veio um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: lista_usuarios.php?erro=ID_invalido");
    exit;
}

$id = intval($_GET['id']);

// Impede que o admin delete ele mesmo (opcional, mas recomendado)
if ($id == $_SESSION['user_id']) {
    header("Location: lista_usuarios.php?erro=nao_pode_se_apagar");
    exit;
}

$sql = $conn->prepare("DELETE FROM users WHERE id = ?");
$sql->bind_param("i", $id);

if ($sql->execute()) {
    header("Location: lista_usuarios.php?deleted=1");
    exit;
} else {
    header("Location: lista_usuarios.php?erro=erro_ao_excluir");
    exit;
}
