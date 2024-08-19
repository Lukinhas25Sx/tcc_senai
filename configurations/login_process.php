<?php
include 'conection.php';


// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Adiciona um log para verificar se o script está sendo executado
error_log("Início do script login_process.php");

// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Adiciona um log para verificar se o script está sendo executado
error_log("Início do script login_process.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura e limpa os dados do formulário
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);

    // Adiciona um log para verificar os dados recebidos
    error_log("Dados recebidos: Email: $email");

    // Consulta o banco de dados para encontrar o usuário com o e-mail fornecido
    $sql = "SELECT senha FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result) {
        // Adiciona um log para verificar o número de linhas retornadas
        error_log("Resultado da consulta: " . $result->num_rows);

        if ($result->num_rows > 0) {
            // Usuário encontrado, pega a senha criptografada
            $row = $result->fetch_assoc();
            $senha_criptografada = $row['senha'];

            // Adiciona um log para verificar se chegou ao ponto de verificação da senha
            error_log("Chegou no ponto de verificação da senha.");
            error_log("Senha fornecida: $senha");
            error_log("Senha criptografada: $senha_criptografada");

            if (password_verify($senha, $senha_criptografada)) {
                // Senha correta, armazena o ID do usuário na sessão
                $_SESSION['user_id'] = $row['id'];
                // Senha correta, redireciona para home.php
                header("Location: ../home.php").PHP_EOL.PHP_EOL;
                exit(); // Garante que o script não continue executando
            } else {
                // Senha incorreta
                echo "As informações de login estão incorretas.";
            }
        } else {
            // Usuário não encontrado
            echo "Usuário não encontrado.".PHP_EOL.PHP_EOL;
        }
    } else {
        // Erro na execução da consulta
        echo "Erro na consulta ao banco de dados.";
    }

    // Fecha a conexão
    $conn->close();
} else {
    echo "Método de requisição inválido.";
}

?>