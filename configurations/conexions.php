<?php
// Dados de conexão com o banco de dados
$servername = 'localhost'; // ou o IP do servidor de banco de dados
$username = 'root'; // seu nome de usuário do banco de dados
$password = ''; // sua senha do banco de dados
$database = 'banco_de_dados_colaboracao'; // Nome do banco de dados

// Criar conexão
$mysqli = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($mysqli->connect_error) {
    die('Falha na conexão: ' . $mysqli->connect_error);
}

?>
