<?php
include '../configurations/conection.php';

$id = $_GET['id'];

$query = "DELETE FROM reservas WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo "Reserva excluída com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
