<?php
// Inclui a conexão com o banco de dados e a proteção de sessão
include '../configurations/conection.php';
include '../configurations/protect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    define('BASE_URL', '/tcc_senai/');
    die("Você não tem permissão para acessar esta página.<p><a href=\"" . BASE_URL . "cadastro/index.php\">Entrar</a></p>");
}

// Busca os usuários de manutenção para selecionar na reserva
$query_manutencao = "SELECT id, nome FROM users WHERE cargo = 'Manutenção'";
$result_manutencao = $pdo->query($query_manutencao);

// Se o método de requisição for POST, processa a criação da reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém dados do formulário
    $usuario_id = $_SESSION['id'];
    $sala = $_POST['sala'];
    $data = $_POST['data'];
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];
    $motivo = $_POST['motivo'];
    $manutencao_id = $_POST['manutencao_id'];

    // Verificação de conflito de horário em reservas confirmadas
    $query_verificacao = "
        SELECT * FROM reservas 
        WHERE data = :data 
        AND sala = :sala
        AND (
            (horario_inicio <= :horario_inicio AND horario_fim > :horario_inicio) 
            OR (horario_inicio < :horario_fim AND horario_fim >= :horario_fim) 
            OR (horario_inicio >= :horario_inicio AND horario_fim <= :horario_fim)
        ) 
        AND status = 'confirmado'
    ";
    $stmt = $pdo->prepare($query_verificacao);
    $stmt->execute(['data' => $data, 'sala' => $sala, 'horario_inicio' => $horario_inicio, 'horario_fim' => $horario_fim]);

    if ($stmt->rowCount() > 0) {
        echo "Erro: Já existe uma reserva confirmada para o mesmo intervalo de horário nesta sala e dia.";
    } else {
        // Insere a reserva no banco de dados se não houver conflito
        $query = "INSERT INTO reservas (usuario_id, sala, data, horario_inicio, horario_fim, motivo, manutencao_id, status)
                  VALUES (:usuario_id, :sala, :data, :horario_inicio, :horario_fim, :motivo, :manutencao_id, 'pendente')";
        
        $stmt = $pdo->prepare($query);
        $params = [
            'usuario_id' => $usuario_id,
            'sala' => $sala,
            'data' => $data,
            'horario_inicio' => $horario_inicio,
            'horario_fim' => $horario_fim,
            'motivo' => $motivo,
            'manutencao_id' => $manutencao_id
        ];

        if ($stmt->execute($params)) {
            // Redireciona após a criação da reserva
            header('Location: reservas.php');
            exit();
        } else {
            echo "Erro ao criar reserva: " . $stmt->errorInfo()[2];
        }
    }
}

// Busca as datas e horários indisponíveis para o calendário
$query_datas_indisponiveis = "
    SELECT data, horario_inicio, horario_fim 
    FROM reservas 
    WHERE status = 'confirmado'
";
$result_datas_indisponiveis = $pdo->query($query_datas_indisponiveis);

$datas_indisponiveis = [];
while ($row = $result_datas_indisponiveis->fetch(PDO::FETCH_ASSOC)) {
    $datas_indisponiveis[] = $row['data'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Reserva</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .sala-options button {
            margin: 5px;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .sala-options button.active {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Criar Reserva</h2>
    <form method="POST">
        <label>Sala:</label>
        <div class="sala-options">
            <button type="button" onclick="selectSala('Biblioteca')">Biblioteca</button>
            <button type="button" onclick="selectSala('Informatica')">Informática</button>
            <button type="button" onclick="selectSala('Laboratorio de Quimica')">Laboratório de Química</button>
            <button type="button" onclick="selectSala('Lego')">Lego</button>
        </div>
        <input type="hidden" name="sala" id="sala" required>

        <label for="data">Data:</label>
        <input type="text" name="data" id="data" required>

        <label for="horario_inicio">Horário de Início:</label>
        <input type="time" name="horario_inicio" id="horario_inicio" required>

        <label for="horario_fim">Horário de Fim:</label>
        <input type="time" name="horario_fim" id="horario_fim" required>

        <label for="motivo">Motivo:</label>
        <textarea name="motivo" id="motivo"></textarea>

        <label for="manutencao_id">Enviar para Manutenção:</label>
        <select name="manutencao_id" id="manutencao_id" required>
            <option value="">Selecione</option>
            <?php while ($row = $result_manutencao->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['nome']); ?></option>
            <?php } ?>
        </select>

        <button type="submit">Criar Reserva</button>
    </form>

    <script>
        // Configuração do Flatpickr para bloquear datas indisponíveis
        document.addEventListener('DOMContentLoaded', function() {
            const datasIndisponiveis = <?php echo json_encode($datas_indisponiveis); ?>;
            flatpickr("#data", {
                dateFormat: "Y-m-d",
                disable: datasIndisponiveis.map(data => new Date(data))
            });
        });

        // Função para selecionar a sala e aplicar o estilo de botão ativo
        function selectSala(sala) {
            document.getElementById('sala').value = sala;
            document.querySelectorAll('.sala-options button').forEach(button => button.classList.remove('active'));
            document.querySelector(`button[onclick="selectSala('${sala}')"]`).classList.add('active');
        }
    </script>
</body>
</html>
