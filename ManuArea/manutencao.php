<?php
session_start();

// Verifica se o usuário está logado e se o cargo é 'Manutenção'
if (!isset($_SESSION['id']) || $_SESSION['cargo'] !== 'Manutenção') {
    define('BASE_URL', '/tcc_senai/');
    die("Você não tem permissão para acessar esta página.<p><a href=\"" . BASE_URL . "cadastro/index.php\">Entrar</a></p>");
}

// Inclua o arquivo de configuração com a conexão ao banco de dados
include '../configurations/conection.php'; // Altere o caminho conforme necessário

// Consulta para buscar mensagens que foram enviadas para a manutenção e que não foram confirmadas
$query = "
    SELECT m.*, u.nome AS remetente_nome 
    FROM mensagens m 
    JOIN users u ON m.remetente_id = u.id 
    WHERE m.destinatario_id = :user_id AND m.confirmada = 0
";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['id']]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC); // Usando FETCH_ASSOC para garantir que recebemos um array associativo

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $mensagem_id = $_POST['mensagem_id'];

    // Atualizar a mensagem para confirmada
    $updateQuery = "UPDATE mensagens SET confirmada = 1 WHERE id = :mensagem_id";
    $updateStmt = $pdo->prepare($updateQuery);
    
    try {
        $updateStmt->execute(['mensagem_id' => $mensagem_id]);
        echo "Mensagem confirmada!";
    } catch (PDOException $e) {
        echo "Erro ao confirmar a mensagem: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manutencao.css">
    <title>Área de Manutenção</title>
</head>
<body>
    <h1>Mensagens Recebidas</h1>
    <?php if (count($mensagens) > 0): ?>
        <ul>
            <?php foreach ($mensagens as $mensagem): ?>
                <li>
                    <strong>De:</strong> <?php echo htmlspecialchars($mensagem['remetente_nome']); ?> <br>
                    <strong>Mensagem:</strong> <?php echo htmlspecialchars($mensagem['mensagem']); ?> <br>
                    <form action="manutencao.php" method="POST">
                        <input type="hidden" name="mensagem_id" value="<?php echo htmlspecialchars($mensagem['id']); ?>">
                        <input type="submit" name="confirmar" value="Confirmar Recebimento">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Não há mensagens novas.</p>
    <?php endif; ?>

    <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></p>
</body>
</html>
