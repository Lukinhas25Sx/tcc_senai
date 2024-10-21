<?php
include '../configurations/conection.php';

// Consulta as reservas com dados de usuário e manutenção
$query = "SELECT r.id, r.sala, r.data, r.horario_inicio, r.horario_fim, 
                 r.motivo, r.status, u.nome AS usuario_nome, m.nome AS manutencao_nome 
          FROM reservas r
          JOIN users u ON r.usuario_id = u.id
          LEFT JOIN users m ON r.manutencao_id = m.id";

$result = $conn->query($query);
?>

<h2>Reservas</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Sala</th>
        <th>Data</th>
        <th>Horário Início</th>
        <th>Horário Fim</th>
        <th>Motivo</th>
        <th>Status</th>
        <th>Usuário</th>
        <th>Manutenção</th>
        <th>Ações</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['sala']; ?></td>
            <td><?php echo $row['data']; ?></td>
            <td><?php echo $row['horario_inicio']; ?></td>
            <td><?php echo $row['horario_fim']; ?></td>
            <td><?php echo $row['motivo']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['usuario_nome']; ?></td>
            <td><?php echo $row['manutencao_nome'] ?? 'Nenhum'; ?></td>
            <td>
                <a href="editar_reserva.php?id=<?php echo $row['id']; ?>">Editar</a> |
                <a href="excluir_reserva.php?id=<?php echo $row['id']; ?>" 
                   onclick="return confirm('Tem certeza que deseja excluir esta reserva?')">Excluir</a>
            </td>
        </tr>
    <?php } ?>
</table>
