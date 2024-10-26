<?php
include '../configurations/conection.php';

$sql = "SELECT data FROM reservas WHERE status = 'confirmado'";
$result = $conexao->query($sql);

$datas_indisponiveis = [];

while($row = $result->fetch_assoc()) {
    $datas_indisponiveis[] = $row['data'];
}

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
