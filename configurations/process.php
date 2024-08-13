<?php
// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o arquivo de conexão
include 'conection.php'; // Verifique se o caminho está correto

// Checa se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se a conexão ainda está aberta
    if ($conn instanceof mysqli && $conn->ping()) {
        // Captura e limpa os dados do formulário
        $nome = $conn->real_escape_string($_POST['nome']);
        $email = $conn->real_escape_string($_POST['email']);
        $senha = $conn->real_escape_string($_POST['senha']);
        $cargo = $conn->real_escape_string($_POST['cargo']);

        // Depuração: exibe os dados capturados
        echo "Nome: $nome, Email: $email, Cargo: $cargo<br>";

        // Criptografa a senha (recomendado para segurança)
        $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

        // Insere os dados no banco de dados
        $sql = "INSERT INTO users (nome, email, senha, tipo_de_usuario) VALUES ('$nome', '$email', '$senha_criptografada', '$cargo')";

        if ($conn->query($sql) === TRUE) {
            echo "Registro criado com sucesso";
        } else {
            echo "Erro: " . $conn->error;
        }
    } else {
        echo "Conexão com o banco de dados não está ativa";
    }

    // Fecha a conexão
    $conn->close();
}
?>
