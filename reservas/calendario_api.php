<?php
include '../configurations/conection.php';

// Consulta as reservas confirmadas no banco de dados, incluindo data e intervalo de horário
$sql = "SELECT data, horario_inicio, horario_fim FROM reservas WHERE status = 'confirmado'";
$result = $conexao->query($sql);

$datas_indisponiveis = [];

// Formata as datas e horários para que o calendário entenda os intervalos ocupados
while ($row = $result->fetch_assoc()) {
    $datas_indisponiveis[] = [
        'start' => $row['data'] . 'T' . $row['horario_inicio'], // Data e início do horário
        'end' => $row['data'] . 'T' . $row['horario_fim'],      // Data e fim do horário
        'display' => 'background',
        'color' => '#FF0000' // Define a cor para indicar indisponibilidade
    ];
}

// Define o cabeçalho e retorna as datas indisponíveis em JSON
header('Content-Type: application/json');
echo json_encode($datas_indisponiveis);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Reservas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.5.1/main.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.5.1/main.min.js"></script>
</head>
<body>

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
