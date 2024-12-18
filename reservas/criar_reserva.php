<?php
ob_start();
// Inclui a conexão com o banco de dados e a proteção de sessão
include '../configurations/conection.php';
include '../configurations/protect.php';
include '../configurations/header.php'; // header.php deve iniciar a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    define('BASE_URL', '/tcc_senai/');
    die("Você não tem permissão para acessar esta página.<p><a href=\"" . BASE_URL . "cadastro/index.php\">Entrar</a></p>");
}

// Consulta o cargo do usuário logado
$query_cargo = "SELECT cargo FROM users WHERE id = :usuario_id";
$stmt_cargo = $pdo->prepare($query_cargo);
$stmt_cargo->execute(['usuario_id' => $_SESSION['id']]);
$cargo = $stmt_cargo->fetchColumn();

// Verifica se o usuário é de manutenção e impede o acesso
if ($cargo === 'Manutenção') {
    die("Usuários de manutenção não têm permissão para criar reservas.");
}

// Busca os usuários de manutenção para selecionar na reserva
$query_manutencao = "SELECT id, nome FROM users WHERE cargo = 'Manutenção'";
$result_manutencao = $pdo->query($query_manutencao);

// Se o método de requisição for POST, processa a criação da reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém dados do formulário
    $usuario_id = $_SESSION['id'];
    $usuario = $_POST['usuario']; // Novo campo 'usuario'
    $sala = $_POST['sala'];
    $data = $_POST['data'];
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
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
        // Exibe a mensagem de erro via alert
        echo "<script>alert('Erro: Já existe uma reserva confirmada para o mesmo intervalo de horário nesta sala e dia.');</script>";
        } else {
        // Insere a reserva no banco de dados se não houver conflito
        $query = "INSERT INTO reservas (usuario, usuario_id, sala, data, horario_inicio, horario_fim, motivo, manutencao_id, status)
                VALUES (:usuario, :usuario_id, :sala, :data, :horario_inicio, :horario_fim, :motivo, :manutencao_id, 'pendente')";

        $stmt = $pdo->prepare($query);
        $params = [
            'usuario' => $usuario,
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

// Busca reservas confirmadas para exibição
$query_reservas_confirmadas = "
    SELECT r.data, r.horario_inicio, r.horario_fim, r.sala, u.nome AS usuario
    FROM reservas r
    JOIN users u ON r.usuario_id = u.id
    WHERE r.status = 'confirmado'
    ORDER BY r.data, r.horario_inicio
";
$result_reservas_confirmadas = $pdo->query($query_reservas_confirmadas);

ob_end_flush();
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
<body class="with-header" style="padding-top: 70px;">
    <h2>Criar Reserva</h2>
    <form method="POST" onsubmit="return validarFormulario()">
    <input type="hidden" name="usuario" value="<?php echo $_SESSION['id']; ?>"> <!-- Adiciona o campo 'usuario' como hidden -->

    <label>Sala:</label>
    <div class="sala-options">
        <button type="button" onclick="selectSala('Biblioteca')">Biblioteca</button>
        <button type="button" onclick="selectSala('Informatica')">Informática</button>
        <button type="button" onclick="selectSala('Laboratorio de Quimica')">Laboratório de Química</button>
        <button type="button" onclick="selectSala('Lego')">Lego</button>
    </div>

    <input type="hidden" name="sala" id="sala" required value="<?php echo isset($_POST['sala']) ? htmlspecialchars($_POST['sala']) : ''; ?>">

    <div class="container_calendario">
        <label for="data"></label>
        <input type="text" name="data" id="data" required style="display: none;" value="<?php echo isset($_POST['data']) ? htmlspecialchars($_POST['data']) : ''; ?>">
        <div id="dataContainer"></div>

        <div class="informacoes">
            <label for="motivo">Motivo:</label>
            <input type="text" name="motivo" id="motivo" placeholder="Informe o motivo da reserva" maxlength="100" oninput="updateCharCount()" value="<?php echo isset($_POST['motivo']) ? htmlspecialchars($_POST['motivo']) : ''; ?>">
            <span id="charCount">0/100</span>

            <script>
                function updateCharCount() {
                    const motivo = document.getElementById('motivo');
                    const charCount = document.getElementById('charCount');
                    charCount.textContent = motivo.value.length + '/100';
                }
            </script>

            <label for="horario_inicio">Horário de Início:</label>
            <input type="text" name="horario_inicio" id="horario_inicio" placeholder="Selecione o horário de início" value="<?php echo isset($_POST['horario_inicio']) ? htmlspecialchars($_POST['horario_inicio']) : ''; ?>">

            <label for="horario_fim">Horário de Fim:</label>
            <input type="text" name="horario_fim" id="horario_fim" placeholder="Selecione o horário de fim" value="<?php echo isset($_POST['horario_fim']) ? htmlspecialchars($_POST['horario_fim']) : ''; ?>">

            <label for="manutencao_id">Usuário do Responsável pela Manutenção:</label>
            <select name="manutencao_id" id="manutencao_id" required>
                <option value="" disabled selected>Selecione o usuário</option>
                <?php while ($row = $result_manutencao->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo (isset($_POST['manutencao_id']) && $_POST['manutencao_id'] == $row['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['nome']); ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit" class="criar">Criar Reserva</button>
        </div>
    </div>

    <!-- Lista de Reservas Confirmadas -->
    <div class="reservas-confirmadas">
        <h3>Reservas Confirmadas</h3>
        <ul>
            <?php while ($reserva = $result_reservas_confirmadas->fetch(PDO::FETCH_ASSOC)) { 
                // Formata a data e hora
                $data_formatada = DateTime::createFromFormat('Y-m-d', $reserva['data'])->format('d/m/Y');
                $horario_formatado = DateTime::createFromFormat('H:i:s', $reserva['horario_inicio'])->format('H:i');
                $horario_fim_formatado = DateTime::createFromFormat('H:i:s', $reserva['horario_fim'])->format('H:i');
            ?>
                <li>
                    <strong>Usuário:</strong> <?php echo htmlspecialchars($reserva['usuario']); ?><br>
                    <strong>Sala:</strong> <?php echo htmlspecialchars($reserva['sala']); ?><br>
                    <strong>Data:</strong> <?php echo $data_formatada . " às " . $horario_formatado . " - " . $horario_fim_formatado; ?><br>
                </li>
            <?php } ?>
        </ul>
    </div>
</form>


    <script>

    document.addEventListener('DOMContentLoaded', function() {
        const datasIndisponiveis = <?php echo json_encode($datas_indisponiveis); ?>;

        flatpickr("#data", {
            dateFormat: "Y-m-d",
            inline: true,
            minDate: "today", // Impede a seleção de datas passadas
            disable: [
                function(date) {
                    return (date.getDay() === 6 || date.getDay() === 0); // Desabilita sábados e domingos
                }
            ],
            appendTo: document.getElementById('dataContainer'),
            onChange: function(selectedDates, dateStr) {
                document.getElementById('data').value = dateStr;
            }
        });
    });

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

        function selectSala(sala) {
            document.getElementById('sala').value = sala;
            document.querySelectorAll('.sala-options button').forEach(button => button.classList.remove('active'));
            document.querySelector(`button[onclick="selectSala('${sala}')"]`).classList.add('active');
        }

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

            if (!horarioInicio) {
                alert("Por favor, selecione o horário de início.");
                return false;
            }

            if (!horarioFim) {
                alert("Por favor, selecione o horário de fim.");
                return false;
            }


            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
    const horarioInicioPicker = flatpickr("#horario_inicio", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minTime: "07:00", // Hora mínima de início (7h)
        maxTime: "17:00", // Hora máxima de fim (17h)
        minuteIncrement: 50, // Intervalo de 50 minutos
        onChange: function(selectedDates, dateStr) {
            // Atualiza o horário de fim para ser após o horário de início
            horarioFimPicker.set("minTime", dateStr);
        }
    });

    const horarioFimPicker = flatpickr("#horario_fim", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minTime: "07:50", // O fim deve ser no mínimo 50 minutos após o início
        maxTime: "17:50", // Hora máxima de fim (17h50)
        minuteIncrement: 50 // Intervalo de 50 minutos
    });
});

    </script>
</body>
</html>
