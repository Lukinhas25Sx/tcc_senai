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
    <form method="POST" onsubmit="return validarFormulario()">
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
        <input type="text" name="horario_inicio" id="horario_inicio" placeholder="Selecione o horário de início" required>

        <label for="horario_fim">Horário de Fim:</label>
        <input type="text" name="horario_fim" id="horario_fim" placeholder="Selecione o horário de fim" required>

        <label for="motivo">Motivo:</label>
        <textarea name="motivo" id="motivo" required></textarea>

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

        // Validação do formulário
        function validarFormulario() {
            const sala = document.getElementById('sala').value;
            const data = document.getElementById('data').value;
            const horarioInicio = document.getElementById('horario_inicio').value;
            const horarioFim = document.getElementById('horario_fim').value;
            const motivo = document.getElementById('motivo').value;

            if (!sala) {
                alert("Por favor, selecione uma sala.");
                return false;
            }

            if (!data) {
                alert("Por favor, selecione uma data.");
                return false;
            }

            if (!horarioInicio) {
                alert("Por favor, selecione o horário de início.");
                return false;
            }

            if (!horarioFim) {
                alert("Por favor, selecione o horário de fim.");
                return false;
            }

            if (!motivo) {
                alert("Por favor, informe o motivo da reserva.");
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Configuração do campo de horário de início
            const horarioInicioPicker = flatpickr("#horario_inicio", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minTime: "08:00",
                maxTime: "18:00",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const horarioInicio = selectedDates[0];
                        horarioFimPicker.set("minTime", horarioInicio.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
                        horarioFimPicker.setDate(null);
                    }
                }
            });

            // Configuração do campo de horário de fim
            const horarioFimPicker = flatpickr("#horario_fim", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minTime: "08:00",
                maxTime: "18:00",
            });
        });
    </script>
</body>
</html>


