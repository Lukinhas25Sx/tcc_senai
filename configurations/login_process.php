<?php
// Inicia a sessão
session_start();
include 'conection.php';

// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("Início do script login_process.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura e limpa os dados do formulário
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);

    error_log("Dados recebidos: Email: $email");

    // Consulta o banco de dados para encontrar o usuário com o e-mail fornecido
    $sql = "SELECT id, nome, senha, cargo FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result) {
        error_log("Resultado da consulta: " . $result->num_rows);

        if ($result->num_rows > 0) {
            // Usuário encontrado, pega a senha criptografada, nome, e o cargo
            $row = $result->fetch_assoc();
            $senha_criptografada = $row['senha'];
            $cargo = $row['cargo']; 
            $nome = $row['nome'];
            $id = $row['id'];

            error_log("Chegou no ponto de verificação da senha.");
            error_log("Senha fornecida: $senha");
            error_log("Senha criptografada: $senha_criptografada");

            if (password_verify($senha, $senha_criptografada)) {
                $_SESSION['id'] = $id;
                $_SESSION['nome'] = $nome;
                $_SESSION['cargo'] = $cargo;  // Armazene o cargo na sessão
            
                if ($cargo == 'Manutenção') {
                    header("Location: ../ManuArea/manutencao.php");
                } elseif ($cargo == 'Professor') {
                    header("Location: ../ProfArea/profarea.php");
                } else {
                    echo "Cargo não reconhecido.";
                }

                exit(); // Garante que o script não continue executando
            } else {
                $_SESSION['error_message'] = "As informações de login estão incorretas.";
                header("Location: ../cadastro/index.php"); // Redireciona de volta à página de login
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Usuário não encontrado.";
            header("Location: ../cadastro/index.php"); // Redireciona de volta à página de login
            exit();
        }
    } else {
        echo "Erro na consulta ao banco de dados: " . $conn->error;
    }

    // Fecha a conexão
    $conn->close();
} else {
    echo "Método de requisição inválido.";
}
?>
