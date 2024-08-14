<?php
include 'conection.php';
// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o arquivo de conexão
include 'conection.php'; // Verifique se o caminho está correto

if (!isset($conn)) {
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
    if ($conn instanceof mysqli && $conn->ping()) {
        // Captura e limpa os dados do formulário
        $nome = $conn->real_escape_string($_POST['nome']);
        $email = $conn->real_escape_string($_POST['email']);
        $senha = $conn->real_escape_string($_POST['senha']);
        $cargo = $conn->real_escape_string($_POST['cargo']);

        // Depuração: exibe os dados capturados
        echo "Nome: $nome, Email: $email, Cargo: $cargo<br>";

        // Verifica se o email já está cadastrado
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Usuário já está cadastrado
            echo "Usuário com este email já está cadastrado.";
        } else {
            // Criptografa a senha (recomendado para segurança)
            $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

            // Insere os dados no banco de dados
            $sql = "INSERT INTO users (nome, email, senha, tipo_de_usuario) VALUES ('$nome', '$email', '$senha_criptografada', '$cargo')";

            if ($conn->query($sql) === TRUE) {
                // Fecha a conexão antes de redirecionar
                $conn->close();

                // Redireciona para a página de sucesso
                header("Location: ../cadastro/index.php?status=success");
                exit();
            } else {
                echo "Erro: " . $conn->error;
            }
        }

        // Fecha a conexão
        $conn->close();
    } else {
        echo "Conexão com o banco de dados não está ativa";
    }
}

?>
