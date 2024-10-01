<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Usuário não autenticado, redireciona para a página de login
    header("Location: cadastro/index.php");
    exit;
}

// O usuário está autenticado, pode acessar o conteúdo
echo "Bem-vindo à página de manutenção, " . htmlspecialchars($_SESSION['email']) . "!";


// Conexão com o banco de dados
$host = 'localhost'; // Alterar para o endereço do seu servidor MySQL
$dbname = 'banco_de_dados_colaboracao'; // Alterar para o nome do seu banco de dados
$username = 'root'; // Alterar para o nome de usuário do banco de dados
$password = ''; // Alterar para a senha do banco de dados

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Não foi possível conectar ao banco de dados: " . $e->getMessage());
}

// Obtém o setor do usuário da sessão
$setor_usuario = $_SESSION['cargo'];

// Prepara a consulta para buscar os dados do formulário com base no setor
$sql = "SELECT * FROM formularios WHERE setor = :setor";
$stmt = $pdo->prepare($sql);
$stmt->execute(['setor' => $setor_usuario]);

// Verifica se existem resultados
if ($stmt->rowCount() > 0) {
    echo "<h1>Dados do Formulário - Setor: " . htmlspecialchars($setor_usuario) . "</h1>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Mensagem</th>
                <th>Data</th>
            </tr>";

    // Exibe os dados de cada linha
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['nome']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['mensagem']) . "</td>
                <td>" . htmlspecialchars($row['data_envio']) . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "Nenhum dado encontrado para o setor: " . htmlspecialchars($setor_usuario);
}

// Fecha a conexão
$pdo = null;
?>