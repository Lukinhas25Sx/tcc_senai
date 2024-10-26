<?php
// Inclui a conexão com o banco de dados e a proteção de sessão
include '../configurations/conection.php';
include '../configurations/protect.php';

// Busca os usuários de manutenção para selecionar na reserva
$query_manutencao = "SELECT id, nome FROM users WHERE cargo = 'Manutenção'";
$result_manutencao = mysqli_query($conexao, $query_manutencao);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém dados do formulário
    $usuario_id = $_SESSION['id'];
    $sala = $_POST['sala'];
    $data = $_POST['data'];
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];
    $motivo = $_POST['motivo'];
    $manutencao_id = $_POST['manutencao_id'];

    // Insere a reserva no banco de dados
    $query = "INSERT INTO reservas (usuario_id, sala, data, horario_inicio, horario_fim, motivo, manutencao_id, status)
              VALUES ('$usuario_id', '$sala', '$data', '$horario_inicio', '$horario_fim', '$motivo', '$manutencao_id', 'pendente')";
    
    if (mysqli_query($conexao, $query)) {
        // Redireciona após a criação da reserva
        header('Location: reservas.php'); // Altere para o nome da sua página
        exit(); // Certifique-se de encerrar o script após redirecionar
    } else {
        echo "Erro ao criar reserva: " . mysqli_error($conexao);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Reserva</title>
    <link rel="stylesheet" href="style.css"> <!-- Inclua seu estilo aqui -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.5.1/main.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.5.1/main.min.js"></script>
</head>
<body>
    <h2>Criar Reserva</h2>
    <form method="POST">
        <label for="sala">Sala:</label>
        <input type="text" name="sala" id="sala" required>

        <label for="data">Data:</label>
        <input type="date" name="data" id="data" required>

        <label for="horario_inicio">Horário de Início:</label>
        <input type="time" name="horario_inicio" id="horario_inicio" required>

        <label for="horario_fim">Horário de Fim:</label>
        <input type="time" name="horario_fim" id="horario_fim" required>

        <label for="motivo">Motivo:</label>
        <textarea name="motivo" id="motivo"></textarea>

        <label for="manutencao_id">Enviar para Manutenção:</label>
        <select name="manutencao_id" id="manutencao_id" required>
            <option value="">Selecione</option>
            <?php while ($row = mysqli_fetch_assoc($result_manutencao)) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['nome']; ?></option>
            <?php } ?>
        </select>

        <button type="submit">Criar Reserva</button>
    </form>

    <div id="calendar"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    // Função para buscar datas indisponíveis
    fetch('calendario_api.php')
        .then(response => response.json())
        .then(datasIndisponiveis => {
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                dateClick: function(info) {
                    // Impede a seleção de dias indisponíveis
                    if (datasIndisponiveis.includes(info.dateStr)) {
                        alert("Este dia está indisponível para reserva.");
                    } else {
                        // Aqui você pode redirecionar para o formulário de reserva ou abrir um modal
                        alert("Dia disponível para reserva!");
                    }
                },
                events: datasIndisponiveis.map(data => ({ start: data, display: 'background', color: '#FF0000' }))
            });

            calendar.render();
        })
        .catch(error => console.error("Erro ao carregar o calendário:", error));
});
</script>

</body>
</html>
