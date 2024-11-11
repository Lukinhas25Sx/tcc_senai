<?php
// Inicia a sessão
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Define o URL de redirecionamento para a página de login
define('BASE_URL', '/tcc_senai/');
header("Location: " . BASE_URL . "cadastro/index.php");
exit();
?>
