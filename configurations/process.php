<?php
include 'conection.php';

// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($conexao)) {
    die("Erro: Conexão não estabelecida.");
}

// Definir e inicializar a variável $index
$index = isset($_GET['index']) ? $_GET['index'] : 0;

if ($index) {
    // Operações com $index
}

// Verifica se o parâmetro de status está definido e exibe a mensagem de sucesso
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $menssagem = "Registro criado com sucesso";
    error_log($menssagem);
}

// Checa se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se a conexão ainda está aberta
    if ($conexao instanceof mysqli && $conexao->ping()) {
        // Captura e limpa os dados do formulário
        $nome = $conexao->real_escape_string($_POST['nome']);
        $email = $conexao->real_escape_string($_POST['email']);
        $senha = $conexao->real_escape_string($_POST['senha']);
        $cargo = $conexao->real_escape_string($_POST['cargo']);

        // Depuração: exibe os dados capturados
        echo "Nome: $nome, Email: $email, Cargo: $cargo<br>";

        // Verifica se o email já está cadastrado
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conexao->query($sql);

        if ($result->num_rows > 0) {
            // Usuário já está cadastrado
            echo "Usuário com este email já está cadastrado.";
        } else {
            // Criptografa a senha (recomendado para segurança)
            $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

            // Insere os dados no banco de dados
            $sql = "INSERT INTO users (nome, email, senha, cargo) VALUES ('$nome', '$email', '$senha_criptografada', '$cargo')";

            if ($conexao->query($sql) === TRUE) {
                // Fecha a conexão antes de redirecionar
                $conexao->close();

                // Redireciona para a página de sucesso
                header("Location: ../cadastro/index.php?status=success");
                exit();
            } else {
                echo "Erro: " . $conexao->error;
            }
        }

        // Fecha a conexão
        $conexao->close();
    } else {
        echo "Conexão com o banco de dados não está ativa";
    }
}

?>
