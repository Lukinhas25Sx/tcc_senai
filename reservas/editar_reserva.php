<?php
include '../configurations/conection.php';

$id = $_GET['id'];
$query = "SELECT * FROM reservas WHERE id = ?";
$stmt = $conexao->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se a consulta retornou um resultado
if ($result->num_rows > 0) {
    $reserva = $result->fetch_assoc();
} else {
    // Se não encontrar o registro, redireciona ou exibe uma mensagem de erro
    echo "Reserva não encontrada.";
    exit;  // Interrompe a execução do script
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <!-- Incluindo o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #4CAF50;
            border: none;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .form-control, .form-select {
            border-radius: 5px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .sala-options button {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .sala-options button.active {
            background-color: #007BFF;
            color: white;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>Editar Reserva</h2>
        <form method="POST" action="processar_edicao.php">
            <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">

            <div class="mb-3">
                <label class="form-label">Sala:</label>
                <div class="sala-options">
                    <button type="button" class="btn btn-outline-secondary" onclick="selectSala('Biblioteca', event)">Biblioteca</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="selectSala('Informatica', event)">Informática</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="selectSala('Laboratorio de Quimica', event)">Laboratório de Química</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="selectSala('Lego', event)">Lego</button>
                </div>
                <input type="hidden" name="sala" id="sala" value="<?php echo $reserva['sala']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="data" class="form-label">Data:</label>
                <input type="date" id="data" name="data" class="form-control" value="<?php echo $reserva['data']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="horario_inicio" class="form-label">Horário Início:</label>
                <input type="time" id="horario_inicio" name="horario_inicio" class="form-control" value="<?php echo $reserva['horario_inicio']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="horario_fim" class="form-label">Horário Fim:</label>
                <input type="time" id="horario_fim" name="horario_fim" class="form-control" value="<?php echo $reserva['horario_fim']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="motivo" class="form-label">Motivo:</label>
                <textarea id="motivo" name="motivo" class="form-control" rows="4"><?php echo $reserva['motivo']; ?></textarea>
            </div>

            <div class="button-group">
                <a href="javascript:history.back()" class="btn btn-secondary" style="background-color: #007BFF;">Voltar</a>
                <button type="submit" class="btn btn-primary" style="background-color: #007BFF;">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<!-- Incluindo o JS do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Adiciona restrição para datas passadas
    document.addEventListener('DOMContentLoaded', function() {
        const hoje = new Date();
        const dataInput = document.getElementById('data');
        
        // Formata a data no formato YYYY-MM-DD
        const dataFormatada = hoje.toISOString().split('T')[0];
        
        // Define o valor mínimo da data como hoje
        dataInput.setAttribute('min', dataFormatada);
        
        const sala = document.getElementById('sala').value;
        const buttons = document.querySelectorAll('.sala-options button');
        
        // Encontra o botão correspondente e aplica a classe 'active'
        buttons.forEach(button => {
            if (button.innerText === sala) {
                button.classList.add('active');
            }
        });
    });

    function selectSala(sala, event) {
        // Defina o valor do campo hidden e atualize os botões de estilo
        document.getElementById('sala').value = sala;

        // Alterar o estilo dos botões para destacar o selecionado
        const buttons = document.querySelectorAll('.sala-options button');
        buttons.forEach(button => {
            button.classList.remove('active');
        });
        
        // Adiciona a classe 'active' ao botão clicado
        event.target.classList.add('active');
    }
</script>

</body>
</html>
