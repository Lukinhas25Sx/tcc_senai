<?php
include '../configurations/conection.php';

$id = $_GET['id'];
$query = "SELECT * FROM reservas WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$reserva = $result->fetch_assoc();
?>

<h2>Editar Reserva</h2>
<form method="POST" action="processar_edicao.php">
    <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">

    <label>Sala:</label>
    <input type="text" name="sala" value="<?php echo $reserva['sala']; ?>" required><br>

    <label>Data:</label>
    <input type="date" name="data" value="<?php echo $reserva['data']; ?>" required><br>

    <label>Horário Início:</label>
    <input type="time" name="horario_inicio" value="<?php echo $reserva['horario_inicio']; ?>" required><br>

    <label>Horário Fim:</label>
    <input type="time" name="horario_fim" value="<?php echo $reserva['horario_fim']; ?>" required><br>

    <label>Motivo:</label>
    <textarea name="motivo"><?php echo $reserva['motivo']; ?></textarea><br>

    <button type="submit">Salvar Alterações</button>
</form>
