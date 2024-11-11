<?php

if(!isset($_SESSION)) {
    session_start();
}

// Verifica se a sessão foi iniciada
if (!isset($_SESSION)) {
    die("Erro: A sessão não foi iniciada corretamente.");
}

// Verifica se a variável 'id' está definida na sessão
if(!isset($_SESSION['id'])) {
    die("Você não pode acessar esta página porque não está logado.<p><a href=\"../cadastro/index.php\">Entrar</a></p>");
}

// Adiciona uma mensagem de depuração para confirmar que o usuário está logado
error_log("Sessão iniciada corretamente. Usuário ID: " . $_SESSION['id']);
?>













