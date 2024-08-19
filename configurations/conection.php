<?php
// Dados de conexão com o banco de dados
$servername = 'localhost';
$username = 'root';
$password = 'senai123';
$database = 'banco_de_dados_colaboracao';

// Criar conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die('Falha na conexão: ' . $conn->connect_error);
}
?>
