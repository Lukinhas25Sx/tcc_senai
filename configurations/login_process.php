<?php
session_start(); // Inicia a sessão

include 'conection.php';

// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("Início do script login_process.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);

    error_log("Dados recebidos: Email: $email");

    $sql = "SELECT senha, cargo FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result) {
        error_log("Resultado da consulta: " . $result->num_rows);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $senha_criptografada = $row['senha'];
            $cargo = $row['cargo'];

            error_log("Chegou no ponto de verificação da senha.");

            if (password_verify($senha, $senha_criptografada)) {
                // Armazena as informações do usuário na sessão
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                $_SESSION['cargo'] = $cargo;

                if ($cargo == 'Manutenção') {
                    header("Location: ../manutencao.php");
                    exit();
                } elseif ($cargo == 'Professor') {
                    header("Location: ../profarea.php");
                    exit();
                } else {
                    echo "Cargo não reconhecido.";
                }
            } else {
                echo "As informações de login estão incorretas.";
            }
        } else {
            echo "Usuário não encontrado.";
        }
    } else {
        echo "Erro na consulta ao banco de dados: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Método de requisição inválido.";
}
?>
