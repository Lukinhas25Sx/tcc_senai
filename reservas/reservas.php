<?php
ob_start();
include '../configurations/conection.php';
include '../configurations/header.php'; // header.php deve iniciar a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    define('BASE_URL', '/tcc_senai/');
    die("Você não tem permissão para acessar esta página.<p><a href=\"" . BASE_URL . "cadastro/index.php\">Entrar</a></p>");
}

// Verifica se a conexão foi estabelecida
if (!isset($pdo)) {
    die("Erro ao conectar ao banco de dados.");
}

// Obtém o ID do usuário logado
$usuario_id = $_SESSION['id'];

// Consulta todas as reservas confirmadas e pendentes para o usuário logado
$query = "SELECT r.*, u.nome AS usuario_nome 
          FROM reservas r 
          JOIN users u ON r.usuario_id = u.id 
          WHERE r.status IN ('confirmado', 'pendente') 
          AND r.usuario_id = :usuario_id 
          ORDER BY r.data_criacao DESC";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtra as reservas confirmadas e pendentes
$reservas_confirmadas = array_filter($reservas, function($reserva) {
    return $reserva['status'] === 'confirmado';
});

$reservas_pendentes = array_filter($reservas, function($reserva) {
    return $reserva['status'] === 'pendente';
});

// Função para formatar data e hora
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

function formatarHora($dataHora) {
    return date('H:i', strtotime($dataHora));
}

// Excluir reserva
if (isset($_POST['excluir'])) {
    $reserva_id = $_POST['reserva_id'];

    // Consulta para excluir a reserva
    $delete_query = "DELETE FROM reservas WHERE id = :reserva_id AND usuario_id = :usuario_id";
    $delete_stmt = $pdo->prepare($delete_query);
    $delete_stmt->bindParam(':reserva_id', $reserva_id, PDO::PARAM_INT);
    $delete_stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $delete_stmt->execute();

    // Redirecionar para a mesma página para evitar reenvio de formulário
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
ob_end_flush();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Reservas</title>
</head>
<body class="with-header" style="padding-top: 60px;">
    <h1>Reservas Confirmadas</h1>
    <div class="container">
        <?php if (empty($reservas_confirmadas)): ?>
            <p>Você não tem reservas confirmadas.</p>
        <?php else: ?>
            <?php foreach ($reservas_confirmadas as $reserva): ?>
                <div class="card">
                    <h2>Reserva ID: <?php echo htmlspecialchars($reserva['id']); ?></h2>
                    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($reserva['usuario_nome']); ?></p>
                    <p><strong>Sala:</strong> <?php echo htmlspecialchars($reserva['sala']); ?></p>
                    <p><strong>Data:</strong> <?php echo formatarData($reserva['data']); ?></p>
                    <p><strong>Horário:</strong> <?php echo formatarHora($reserva['horario_inicio']) . ' ~ ' . formatarHora($reserva['horario_fim']); ?></p>
                    <p><strong>Motivo:</strong> <?php echo nl2br(htmlspecialchars($reserva['motivo'])); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($reserva['status']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h1>Reservas Pendentes</h1>
    <div class="container">
        <?php if (empty($reservas_pendentes)): ?>
            <p>Você não tem reservas pendentes.</p>
        <?php else: ?>
            <?php foreach ($reservas_pendentes as $reserva): ?>
                <div class="card">
                    <h2>Reserva ID: <?php echo htmlspecialchars($reserva['id']); ?></h2>
                    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($reserva['usuario_nome']); ?></p>
                    <p><strong>Sala:</strong> <?php echo htmlspecialchars($reserva['sala']); ?></p>
                    <p><strong>Data:</strong> <?php echo formatarData($reserva['data']); ?></p>
                    <p><strong>Horário:</strong> <?php echo formatarHora($reserva['horario_inicio']) . ' ~ ' . formatarHora($reserva['horario_fim']); ?></p>
                    <p><strong>Motivo:</strong> <?php echo nl2br(htmlspecialchars($reserva['motivo'])); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($reserva['status']); ?></p>

                    <!-- Botões de Editar e Excluir -->
                    <div class="button-group">
                        <form method="post" action="editar_reserva.php?id=<?php echo htmlspecialchars($reserva['id']); ?>">
                            <input type="submit" class="btn" value="Editar">
                        </form>
                        <form method="post">
                            <input type="hidden" name="reserva_id" value="<?php echo htmlspecialchars($reserva['id']); ?>">
                            <input type="submit" name="excluir" class="btn" value="Excluir" onclick="return confirm('Você tem certeza que deseja excluir esta reserva?');">
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="criar_reserva.php" class="botao_link">Criar Nova Reserva</a>
    <a href="../ProfArea/profarea.php" class="botao_link">Sair</a>
</body>
</html>
