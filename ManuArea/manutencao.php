<?php
session_start();

// Verifica se o usuário está logado e se o cargo é 'Manutenção'
if (!isset($_SESSION['id']) || $_SESSION['cargo'] !== 'Manutenção') {
    define('BASE_URL', '/tcc_senai/');
    die("Você não tem permissão para acessar esta página.<p><a href=\"" . BASE_URL . "cadastro/index.php\">Entrar</a></p>");
}

// Inclua o arquivo de configuração com a conexão ao banco de dados
include '../configurations/conection.php';

// Consulta para buscar mensagens que foram enviadas para a manutenção e que não foram confirmadas
$query = "
    SELECT m.*, u.nome AS remetente_nome 
    FROM mensagens m 
    JOIN users u ON m.remetente_id = u.id 
    WHERE m.destinatario_id = :user_id AND m.confirmada = 0
";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['id']]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Confirmar mensagem
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

// Consultar reservas, incluindo o nome do usuário que fez a reserva
$reservaQuery = "
    SELECT r.*, u.nome AS usuario_nome 
    FROM reservas r 
    JOIN users u ON r.usuario_id = u.id 
    WHERE r.status = 'pendente'
"; // Modifique a condição conforme necessário
$reservaStmt = $pdo->prepare($reservaQuery);
$reservaStmt->execute();
$reservas = $reservaStmt->fetchAll(PDO::FETCH_ASSOC);

// Confirmar reserva
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_reserva'])) {
    $reserva_id = $_POST['reserva_id'];

    // Adiciona verificação para garantir que apenas usuários de 'Manutenção' podem confirmar reservas
    if ($_SESSION['cargo'] === 'Manutenção') {
        $sql = "UPDATE reservas SET status = 'confirmado' WHERE id = :reserva_id";
        $updateReservaStmt = $pdo->prepare($sql);
        try {
            $updateReservaStmt->execute(['reserva_id' => $reserva_id]);
            echo "Reserva confirmada!";
        } catch (PDOException $e) {
            echo "Erro ao confirmar reserva: " . $e->getMessage();
        }
    } else {
        echo "Você não tem permissão para confirmar esta reserva.";
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
                        <input type="submit" name="confirmar" value="Confirmar Mensagem">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Não há mensagens novas.</p>
    <?php endif; ?>

    <h2>Reservas Pendentes</h2>
    <?php if (count($reservas) > 0): ?>
        <div class="reservas-container">
            <?php foreach ($reservas as $reserva): ?>
                <div class="reserva-card">
                    <strong style="font-size: 1.2em;">Solicitante:</strong> <?php echo htmlspecialchars($reserva['usuario_nome']); ?> <br> <!-- Nome do usuário -->
                    <strong>Motivo:</strong> <?php echo htmlspecialchars($reserva['motivo']); ?> <br>
                    <strong>Sala:</strong> <?php echo htmlspecialchars($reserva['sala']); ?> <br>
                    <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($reserva['data'])) . '<br>'; ?>
                    <strong>Horário:</strong> <?php echo htmlspecialchars(date('H:i', strtotime($reserva['horario_inicio']))) . ' ~ ' . htmlspecialchars(date('H:i', strtotime($reserva['horario_fim']))); ?> <br>
                    <form action="manutencao.php" method="POST">
                        <input type="hidden" name="reserva_id" value="<?php echo htmlspecialchars($reserva['id']); ?>">
                        <input type="submit" name="confirmar_reserva" value="Confirmar Reserva">
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Não há reservas pendentes.</p>
    <?php endif; ?>

    <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></p>
</body>
</html>
