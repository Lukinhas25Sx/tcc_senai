<?php
ob_start();
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
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
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
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
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
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Professor</title>
    <script>
        if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        function disableSubmit() {
            document.getElementById("submit-button").disabled = true;
            document.getElementById("message-form").submit(); // Envia o formulário
        }
    function toggleEditForm(mensagemId) {
        var form = document.getElementById('form-edit-' + mensagemId);
        var editButton = document.getElementById('edit-button-' + mensagemId);
        
        // Alterna a visibilidade do formulário
        if (form.style.display === "none") {
            form.style.display = "block";
            editButton.textContent = "Cancelar";
        } else {
            form.style.display = "none";
            editButton.textContent = "Editar";
        }
    }

    </script>
    <style>

        html body.with-header{
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #ececec !important;
            padding-top: 100px;
        }

        h1, h2 {
            color: #333;
        }
        /* Estilo geral para a div de mensagens */
        .mensagens {
            background-color: #fff !important;
            border-radius: 10px;
            max-width: 960px; /* Largura máxima para a div de mensagens */
            margin: 0 auto; /* Centraliza a div de mensagens */
            padding: 20px;
            box-sizing: border-box; /* Inclui padding no cálculo da largura */
        }

        /* Layout da seção de mensagens */
        .messages-section {
            display: flex;
            gap: 20px; /* Espaço entre os elementos */
            flex-wrap: wrap; /* Permite que os cards quebrem para a linha seguinte se necessário */
            width: 100%; /* Ocupará toda a largura da div mensagens */
            box-sizing: border-box;
        }

        /* Estilo para as colunas esquerda e direita */
        .messages-section-esquerda, .messages-section-direita {
            width: calc(50% - 10px); /* Divide igualmente, com um pequeno espaço entre */
            box-sizing: border-box; /* Inclui padding e margens no cálculo da largura */
            padding: 20px;
        }

        /* Estilo dos cards dentro das seções */
        .card {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            width: 100%; /* Garante que o card ocupe toda a largura da sua seção */
            max-height: 260px;
            overflow-y: auto;
            overflow-wrap: break-word;
            box-sizing: border-box; /* Garante que padding e borda não aumentem o tamanho da div */
        }

        /* Responsividade para telas menores (mobile) */
        @media (max-width: 768px) {
        .messages-section-esquerda, .messages-section-direita {
            width: 100%; /* Em telas pequenas, as colunas se tornam full width */
            gap: 10px; /* Menor espaço entre as colunas em telas pequenas */
            }
        }



        .card strong, .card p {
            overflow-wrap: break-word; /* Quebra palavras longas */
            word-break: break-all; /* Quebra qualquer palavra que ultrapasse o limite */
            display: block; /* Garante que as quebras de linha funcionem */
        }

        .action-buttons {
            margin-top: 10px; /* Espaço acima dos botões de ação */
        }

        form {
            display: inline; /* Permite que os formulários sejam exibidos em linha */
        }

        #edit-button {
            background-color: #28a745; /* Cor verde para botões */
            color: white; /* Texto branco para os botões */
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        #edit-button:hover {
            background-color: #218838; /* Cor mais escura ao passar o mouse */
        }

        input[type="submit"] {
            background-color: #28a745; /* Cor verde para botões */
            color: white; /* Texto branco para os botões */
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838; /* Cor mais escura ao passar o mouse */
        }

        label {
            display: block; /* Garante que cada rótulo ocupe uma linha */
            margin-top: 10px; /* Espaço acima dos rótulos */
        }

        textarea {
            width: 100%; /* Garante que o textarea ocupe toda a largura disponível */
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            resize: vertical;
            font-size: 14px;
            box-sizing: border-box; /* Evita que o padding e borda façam o textarea vazar */
        }


        textarea:focus {
            border-color: #80bdff; /* Borda azul ao focar no textarea */
            outline: none; /* Remove o contorno padrão */
        }

        p {
            margin-top: 10px; /* Espaço acima de mensagens de sucesso ou erro */
        }
        /* Estilo do select */
        select {
            width: 100%; /* Garante que o select ocupe toda a largura disponível */
            padding: 10px; /* Espaçamento interno para tornar o campo mais legível */
            font-size: 14px; /* Tamanho da fonte para tornar o texto legível */
            border-radius: 5px; /* Bordas arredondadas */
            border: 1px solid #ccc; /* Borda cinza suave */
            background-color: #f9f9f9; /* Fundo leve e neutro */
            box-sizing: border-box; /* Inclui o padding e a borda no tamanho total do select */
            transition: border-color 0.3s ease, background-color 0.3s ease; /* Transição suave */
        }

        /* Estilo do select ao focar */
        select:focus {
            border-color: #007bff; /* Cor da borda azul ao focar */
            outline: none; /* Remove o contorno padrão */
            background-color: #e9f7ff; /* Fundo leve azul ao focar */
        }

        /* Estilo das opções do select */
        option {
            padding: 10px; /* Espaçamento interno das opções */
            background-color: #fff; /* Fundo branco das opções */
            color: #333; /* Cor do texto das opções */
        }

        /* Estilo do select ao passar o mouse */
        select:hover {
            border-color: #80bdff; /* Cor de borda mais clara ao passar o mouse */
        }
        ul{
            padding: 0;
        }
        .mensagem-not{
            padding-bottom: 10px;
        }
    </style>
</head>
<body class="with-header">
    <div class="mensagens">
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
    </div>
<div class="messages-section"> <!-- Seção de Mensagens Confirmadas (esquerda) -->
    <div class="messages-section-esquerda">
            <h2>Mensagens Confirmadas:</h2>
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
    <!-- Seção de Mensagens Não Confirmadas (Direita) -->
<div class="messages-section-direita">
    <h2>Mensagens Não Confirmadas:</h2>
        <ul>
        <?php foreach ($mensagens_enviadas as $mensagem): ?>
            <?php if (!$mensagem['confirmada']): ?>
                <li class="card">
                    <strong>Para:</strong> <?php echo htmlspecialchars($mensagem['destinatario_nome']); ?> <br>
                    <strong>Mensagem:</strong> <?php echo nl2br(htmlspecialchars($mensagem['mensagem'])); ?> <br>
                    <strong>Status:</strong> Não Confirmada
                    <div class="action-buttons">
                        <!-- Botão de editar -->
                        <button type="button" id="edit-button-<?php echo $mensagem['id']; ?>" onclick="toggleEditForm(<?php echo $mensagem['id']; ?>)">Editar</button>
                        
                        <!-- Formulário de edição oculto inicialmente -->
                        <form action="profarea.php" method="POST" style="display:none;" id="form-edit-<?php echo $mensagem['id']; ?>">
                            <input type="hidden" name="mensagem_id" value="<?php echo htmlspecialchars($mensagem['id']); ?>">
                            <input type="hidden" name="action" class="mensagem-not" value="edit">

                            <div class="textarea">
                                <textarea name="mensagem" rows="2" required><?php echo htmlspecialchars($mensagem['mensagem']); ?></textarea>
                            </div>

                            <input type="submit" value="Salvar">
                        </form>

                        <!-- Formulário de exclusão -->
                        <form action="profarea.php" method="POST" style="display:inline;">
                            <input type="hidden" name="mensagem_id" value="<?php echo htmlspecialchars($mensagem['id']); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="submit" value="Excluir" style="margin-top: 10px;" onclick="return confirm('Tem certeza que deseja excluir esta mensagem?');">
                        </form>
                    </div>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
</div>
</body>
</html>
