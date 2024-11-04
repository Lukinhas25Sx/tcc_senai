<?php
// Inclua o arquivo de configuração com a conexão ao banco de dados
include '../configurations/conection.php'; // Altere o caminho conforme necessário
include '../configurations/header.php'; // header.php deve iniciar a sessão

// Verifica se o usuário está logado e se o cargo é 'Professor'
if (!isset($_SESSION['id']) || $_SESSION['cargo'] !== 'Professor') {
    define('BASE_URL', '/tcc_senai/');
    die("Você não tem permissão para acessar esta página.<p><a href=\"" . BASE_URL . "cadastro/index.php\">Entrar</a></p>");
}

// Verifica se a conexão foi estabelecida
if (!isset($pdo)) {
    die("Erro ao conectar ao banco de dados.");
}

// Processa o envio da mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Envio de mensagem
    if (isset($_POST['action']) && $_POST['action'] === 'send') {
        if (empty($_POST['destinatario_id']) || empty($_POST['mensagem'])) {
            $error_message = "ID do destinatário ou conteúdo da mensagem não estão definidos.";
        } else {
            $remetente_id = $_SESSION['id'];  // O ID do usuário logado (Professor)
            $destinatario_id = $_POST['destinatario_id'];  // O ID do destinatário (quem fez o pedido)
            $mensagem = $_POST['mensagem'];  // Mensagem escrita pelo professor

            // Exemplo de inserção de mensagem no banco de dados
            $query = "INSERT INTO mensagens (remetente_id, destinatario_id, mensagem) VALUES (:remetente_id, :destinatario_id, :mensagem)";
            $stmt = $pdo->prepare($query);

            try {
                $stmt->execute([
                    'remetente_id' => $remetente_id,
                    'destinatario_id' => $destinatario_id,
                    'mensagem' => $mensagem
                ]);
                $success_message = "Mensagem enviada com sucesso!";
                // Redireciona após o envio da mensagem para evitar o envio duplicado
                header("Location: " . $_SERVER['PHP_SELF']);
                exit; // É importante sair após o redirecionamento
            } catch (PDOException $e) {
                $error_message = "Erro ao enviar a mensagem: " . $e->getMessage();
            }
        }
    }

    // Exclusão de mensagem
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $mensagem_id = $_POST['mensagem_id'];

        $deleteQuery = "DELETE FROM mensagens WHERE id = :mensagem_id";
        $deleteStmt = $pdo->prepare($deleteQuery);

        try {
            $deleteStmt->execute(['mensagem_id' => $mensagem_id]);
            $success_message = "Mensagem excluída com sucesso!";
        } catch (PDOException $e) {
            $error_message = "Erro ao excluir a mensagem: " . $e->getMessage();
        }
    }

    // Edição de mensagem
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $mensagem_id = $_POST['mensagem_id'];
        $mensagem_nova = $_POST['mensagem'];

        $updateQuery = "UPDATE mensagens SET mensagem = :mensagem WHERE id = :mensagem_id AND confirmada = 0";
        $updateStmt = $pdo->prepare($updateQuery);

        try {
            $updateStmt->execute(['mensagem' => $mensagem_nova, 'mensagem_id' => $mensagem_id]);
            $success_message = "Mensagem editada com sucesso!";
        } catch (PDOException $e) {
            $error_message = "Erro ao editar a mensagem: " . $e->getMessage();
        }
    }
}

// Consultar todos os destinatários (Manutenção) para preencher o formulário
$destinatarios = [];
$query = "SELECT id, nome FROM users WHERE cargo = 'Manutenção'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$destinatarios = $stmt->fetchAll(PDO::FETCH_ASSOC); // Usando FETCH_ASSOC para garantir que recebemos um array associativo

// Consultar mensagens enviadas pelo professor, incluindo o nome do destinatário
$query = "
    SELECT m.*, u.nome AS destinatario_nome 
    FROM mensagens m 
    JOIN users u ON m.destinatario_id = u.id 
    WHERE m.remetente_id = :remetente_id 
    ORDER BY m.id DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute(['remetente_id' => $_SESSION['id']]);
$mensagens_enviadas = $stmt->fetchAll(PDO::FETCH_ASSOC); // Usando FETCH_ASSOC para garantir que recebemos um array associativo
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profarea.css">
    <title>Área do Professor</title>
    <script>
        function disableSubmit() {
            document.getElementById("submit-button").disabled = true;
            document.getElementById("message-form").submit(); // Envia o formulário
        }
    </script>
</head>
<body>
    <h1>Enviar Mensagem</h1>
    <form action="profarea.php" method="POST" id="message-form" onsubmit="disableSubmit();">
        <input type="hidden" name="action" value="send"> <!-- Adiciona um campo de ação -->
        <label for="destinatario_id">Selecione o Destinatário:</label>
        <select name="destinatario_id" required>
            <?php foreach ($destinatarios as $destinatario): ?>
                <option value="<?php echo htmlspecialchars($destinatario['id']); ?>">
                    <?php echo htmlspecialchars($destinatario['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="mensagem">Mensagem:</label>
        <textarea name="mensagem" rows="4" cols="50" required></textarea>
        <br><br>
        <input type="submit" value="Enviar Mensagem" id="submit-button">
    </form>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Seção de Mensagens Confirmadas -->
    <div class="messages-section">
        <h2>Mensagens Confirmadas</h2>
        <ul>
            <?php foreach ($mensagens_enviadas as $mensagem): ?>
                <?php if ($mensagem['confirmada']): ?>
                    <li class="card">
                        <strong>Para:</strong> <?php echo htmlspecialchars($mensagem['destinatario_nome']); ?> <br>
                        <strong>Mensagem:</strong> <?php echo nl2br(htmlspecialchars($mensagem['mensagem'])); ?> <br>
                        <strong>Status:</strong> Confirmada
                        <div class="action-buttons">
                            <form action="profarea.php" method="POST" style="display:inline;">
                                <input type="hidden" name="mensagem_id" value="<?php echo htmlspecialchars($mensagem['id']); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="submit" value="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta mensagem?');">
                            </form>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Seção de Mensagens Não Confirmadas -->
    <div class="messages-section">
        <h2>Mensagens Não Confirmadas</h2>
        <ul>
            <?php foreach ($mensagens_enviadas as $mensagem): ?>
                <?php if (!$mensagem['confirmada']): ?>
                    <li class="card">
                        <strong>Para:</strong> <?php echo htmlspecialchars($mensagem['destinatario_nome']); ?> <br>
                        <strong>Mensagem:</strong> <?php echo nl2br(htmlspecialchars($mensagem['mensagem'])); ?> <br>
                        <strong>Status:</strong> Não Confirmada
                        <div class="action-buttons">
                            <form action="profarea.php" method="POST" style="display:inline;">
                                <input type="hidden" name="mensagem_id" value="<?php echo htmlspecialchars($mensagem['id']); ?>">
                                <input type="hidden" name="action" value="edit">
                                <textarea name="mensagem" rows="2" required><?php echo htmlspecialchars($mensagem['mensagem']); ?></textarea>
                                <input type="submit" value="Editar">
                            </form>
                            <form action="profarea.php" method="POST" style="display:inline;">
                                <input type="hidden" name="mensagem_id" value="<?php echo htmlspecialchars($mensagem['id']); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="submit" value="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta mensagem?');">
                            </form>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Botão para fazer reserva -->
    <a href="../reservas/reservas.php" style="padding: 10px; background-color: blue; color: white; text-decoration: none; border-radius: 5px;">Fazer Reserva</a>
    <a href="logout.php">Sair</a>
    <a href="../configurations/perfil.php">Perfil</a>
</body>
</html>
