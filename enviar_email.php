<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Dados de conexão com o banco de dados
$host = 'localhost'; // Alterar para o endereço do seu servidor MySQL
$dbname = 'banco_de_dados_colaboracao'; // Alterar para o nome do seu banco de dados
$username = 'root'; // Alterar para o nome de usuário do banco de dados
$password = ''; // Alterar para a senha do banco de dados

// Conectar ao banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Não foi possível conectar ao banco de dados: " . $e->getMessage());
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta e sanitiza os dados do formulário
    $nome = htmlspecialchars($_POST['nome']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mensagem = htmlspecialchars($_POST['mensagem']);
    $setor = $_POST['setor']; // Coleta o setor escolhido

    // Verifica se o e-mail do usuário é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("E-mail inválido.");
    }

    // Define o destinatário com base no setor

    //não pode ser gmail !!!
    $destinatarios = [

        //colocar o email do nivaldo
        'manutencao' => 'eduorganiza@outlook.com',

        //colocar o email do guilherme
        'agendamento' => 'xyz@empresa.com',
    ];

    if (!array_key_exists($setor, $destinatarios)) {
        die("Setor inválido.");
    }

    // Instancia o PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Servidor SMTP do Outlook
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mmagriantonelli@gmail.com'; // Conta de e-mail usada para autenticação SMTP
        $mail->Password   = 'bgsa eeon sxkd atoc';         // Senha da conta de e-mail usada para autenticação
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Habilita o log detalhado
        //$mail->SMTPDebug = 2;

        // Configura o remetente e destinatário
        $mail->setFrom('mmagriantonelli@gmail.com', 'Edu Organiza'); // E-mail fixo do remetente
        $mail->addAddress($destinatarios[$setor]); // Adiciona o e-mail do setor escolhido

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'EduOrganiza - Contato';
        $mail->Body    = "<strong>Nome:</strong> $nome<br><strong>E-mail:</strong> $email<br><strong>Mensagem:</strong> $mensagem";
        $mail->AltBody = "Nome: $nome\nE-mail: $email\nMensagem: $mensagem";

        // Envia o e-mail
        $mail->send();

        // Insere os dados no banco de dados
        $stmt = $pdo->prepare("INSERT INTO formularios (nome, email, mensagem, setor) VALUES (:nome, :email, :mensagem, :setor)");
        $stmt->execute(['nome' => $nome, 'email' => $email, 'mensagem' => $mensagem, 'setor' =>  $setor]);

        echo 'E-mail enviado e dados salvos com sucesso!';
    } catch (Exception $e) {
        echo "O e-mail não pôde ser enviado. Erro: {$mail->ErrorInfo}";
    }
} else {
    echo "Método de solicitação inválido.";
}
?>