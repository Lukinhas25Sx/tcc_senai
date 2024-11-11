<?php
include '../configurations/conection.php';


$id = $_GET['id'];

$query = "DELETE FROM reservas WHERE id = ?";
$stmt = $conexao->prepare($query);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo "Reserva excluída com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conexao->close();
?>
