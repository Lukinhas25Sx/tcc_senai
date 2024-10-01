<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Contato</title>
</head>
<body>
    <h1>Formulário de Contato</h1>
    <form action="enviar_email.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="mensagem">Mensagem:</label><br>
        <textarea id="mensagem" name="mensagem" rows="4" cols="50" required></textarea><br><br>

        <label for="setor">Setor:</label>
        <select id="setor" name="setor" required>
            <option value="">Selecione um setor</option>
            <option value="manutencao">Manutenção</option>
            <option value="agendamento">Agendamento</option>
        </select><br><br>

        <input type="submit" value="Enviar">
    </form>
</body>
</html>
