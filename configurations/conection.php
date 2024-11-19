<?php
// Dados de conexão com o banco de dados
$servername = 'localhost:3310';
$username = 'root';
$password = '';
$database = 'banco_de_dados_colaboracao';

// Criar conexão com MySQL usando mysqli
$conexao = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conexao->connect_error) {
    die('Falha na conexão: ' . $conexao->connect_error);
}

// Criar conexão com MySQL usando PDO (caso precise em algum lugar do projeto)
try {
    $pdo = new PDO("mysql:host=localhost;port=3310;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

?>