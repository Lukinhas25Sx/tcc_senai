<?php
// Dados de conexão com o banco de dados
$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'banco_de_dados_colaboracao';

// Criar conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die('Falha na conexão: ' . $conn->connect_error);
}

try {
    // Altere os parâmetros conforme necessário
    $pdo = new PDO('mysql:host=localhost;dbname=banco_de_dados_colaboracao;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

?>
