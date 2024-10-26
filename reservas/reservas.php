<?php
// Inclua o arquivo de configuração com a conexão ao banco de dados
include '../configurations/conection.php'; // Altere o caminho conforme necessário
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    define('BASE_URL', '/tcc_senai/');
    die("Você não tem permissão para acessar esta página.<p><a href=\"" . BASE_URL . "cadastro/index.php\">Entrar</a></p>");
}

// Verifica se a conexão foi estabelecida
if (!isset($pdo)) {
    die("Erro ao conectar ao banco de dados.");
}

// Consultar todas as reservas confirmadas e pendentes
$query = "SELECT r.*, u.nome AS usuario_nome 
          FROM reservas r 
          JOIN users u ON r.usuario_id = u.id 
          WHERE r.status IN ('confirmado', 'pendente') 
          ORDER BY r.data_criacao DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtra as reservas confirmadas e pendentes
$reservas_confirmadas = array_filter($reservas, function($reserva) {
    return $reserva['status'] === 'confirmado';
});

$reservas_pendentes = array_filter($reservas, function($reserva) {
    return $reserva['status'] === 'pendente';
});
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Reservas</title>
</head>
<body>
    <h1>Reservas Confirmadas</h1>
    <div class="container">
        <?php foreach ($reservas_confirmadas as $reserva): ?>
            <div class="card">
                <h2>Reserva ID: <?php echo htmlspecialchars($reserva['id']); ?></h2>
                <p><strong>Usuário:</strong> <?php echo htmlspecialchars($reserva['usuario_nome']); ?></p>
                <p><strong>Sala:</strong> <?php echo htmlspecialchars($reserva['sala']); ?></p>
                <p><strong>Data:</strong> <?php echo htmlspecialchars($reserva['data']); ?></p>
                <p><strong>Horário Início:</strong> <?php echo htmlspecialchars($reserva['horario_inicio']); ?></p>
                <p><strong>Horário Fim:</strong> <?php echo htmlspecialchars($reserva['horario_fim']); ?></p>
                <p><strong>Motivo:</strong> <?php echo nl2br(htmlspecialchars($reserva['motivo'])); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($reserva['status']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <h1>Reservas Pendentes</h1>
    <div class="container">
        <?php foreach ($reservas_pendentes as $reserva): ?>
            <div class="card">
                <h2>Reserva ID: <?php echo htmlspecialchars($reserva['id']); ?></h2>
                <p><strong>Usuário:</strong> <?php echo htmlspecialchars($reserva['usuario_nome']); ?></p>
                <p><strong>Sala:</strong> <?php echo htmlspecialchars($reserva['sala']); ?></p>
                <p><strong>Data:</strong> <?php echo htmlspecialchars($reserva['data']); ?></p>
                <p><strong>Horário Início:</strong> <?php echo htmlspecialchars($reserva['horario_inicio']); ?></p>
                <p><strong>Horário Fim:</strong> <?php echo htmlspecialchars($reserva['horario_fim']); ?></p>
                <p><strong>Motivo:</strong> <?php echo nl2br(htmlspecialchars($reserva['motivo'])); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($reserva['status']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Botão para criar nova reserva -->
    <a href="criar_reserva.php">Criar Nova Reserva</a>

    <a href="logout.php">Sair</a>
</body>
</html>
