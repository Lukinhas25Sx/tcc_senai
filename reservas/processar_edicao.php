<?php
include '../configurations/conection.php';

$id = $_POST['id'];
$sala = $_POST['sala'];
$data = $_POST['data'];
$horario_inicio = $_POST['horario_inicio'];
$horario_fim = $_POST['horario_fim'];
$motivo = $_POST['motivo'];

$query = "UPDATE reservas SET sala = ?, data = ?, horario_inicio = ?, horario_fim = ?, motivo = ? 
          WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('sssssi', $sala, $data, $horario_inicio, $horario_fim, $motivo, $id);

if ($stmt->execute()) {
    echo "Reserva atualizada com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>