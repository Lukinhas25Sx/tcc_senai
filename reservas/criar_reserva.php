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

        <label for="data"></label>
        <input type="text" name="data" id="data" required style="display: none;">
        <div id="dataContainer"></div>


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
        document.addEventListener('DOMContentLoaded', function() {
        const datasIndisponiveis = <?php echo json_encode($datas_indisponiveis); ?>;

        flatpickr("#data", {
            dateFormat: "Y-m-d",
            inline: true,
            minDate: "today",
            disable: [
                ...datasIndisponiveis.map(data => new Date(data)),
                function(date) {
                    return (date.getDay() === 6 || date.getDay() === 0);
                }
            ],
            appendTo: document.getElementById('dataContainer'),
            onChange: function(selectedDates, dateStr) {
                document.getElementById('data').value = dateStr;
            }
        });
    });


        // Função para selecionar a sala e aplicar o estilo de botão ativo
        function selectSala(sala) {
            document.getElementById('sala').value = sala;
            document.querySelectorAll('.sala-options button').forEach(button => button.classList.remove('active'));
            document.querySelector(`button[onclick="selectSala('${sala}')"]`).classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
    // Configurar datas indisponíveis no calendário
    const datasIndisponiveis = <?php echo json_encode($datas_indisponiveis); ?>;

    // Calendário para a data
    flatpickr("#data", {
        dateFormat: "Y-m-d",
        inline: true,
        minDate: "today",
        disable: [
            ...datasIndisponiveis.map(data => new Date(data)),
            function(date) {
                return (date.getDay() === 6 || date.getDay() === 0);
            }
        ],
        appendTo: document.getElementById('dataContainer'),
        onChange: function(selectedDates, dateStr) {
            document.getElementById('data').value = dateStr;
        }
    });

    // Configuração de intervalo de tempo para o campo de horário
    flatpickr("#horario_intervalo", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        mode: "range", // Modo intervalo para seleção de início e fim
        minTime: "08:00", // Horário mínimo
        maxTime: "18:00", // Horário máximo
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                // Se os dois horários foram selecionados, exiba-os no formato desejado
                const horarioInicio = selectedDates[0].toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const horarioFim = selectedDates[1].toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                // Exibe o intervalo no campo de input e salva em campos ocultos
                document.getElementById('horario_intervalo').value = `${horarioInicio} - ${horarioFim}`;
                document.getElementById('horario_inicio').value = horarioInicio;
                document.getElementById('horario_fim').value = horarioFim;
            }
        }
    });
});

    </script>
</body>
</html>

