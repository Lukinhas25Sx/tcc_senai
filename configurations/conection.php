<?php
// Dados de conexão com o banco de dados
$servername = 'ec2-44-202-161-195.compute-1.amazonaws.com'; // ou o IP da instância
$username = 'Lucas';
$password = 'lucas2007l';
$database = 'banco_de_dados_colaboracao';
$port = 3306; // a porta padrão do MySQL

// Criar conexão com MySQL usando mysqli
$conexao = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexão
if ($conexao->connect_error) {
    die('Falha na conexão: ' . $conexao->connect_error);
}

// Criar conexão com MySQL usando PDO (caso precise em algum lugar do projeto)
try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
