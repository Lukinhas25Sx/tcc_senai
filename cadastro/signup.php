<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cadastro.css">
    <title>Cadastrar Usuário</title>
</head>
<body>
    <div class="header">
        <h1>Cadastro de Usuário</h1>
    </div>
    <div class="container">
        <form class="login-form" action="/tcc_senai/configurations/process.php" method="post" onsubmit="handleRegister(event)">
            <h2>Preencha seus dados</h2>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        
            <label>Cargo:</label>
            <div class="cargo-buttons">
                <button type="button" class="cargo-button" id="professor-button" onclick="setCargo('professor', 'professor-button')">Professor</button>
                <button type="button" class="cargo-button" id="manutencao-button" onclick="setCargo('manutencao', 'manutencao-button')">Manutenção</button>
            </div>
            <input type="hidden" id="cargo" name="cargo" required>
        
            <button type="submit">Enviar</button>
        
            <p>Já tem uma conta? <a href="index.php">Faça login aqui</a></p>
        </form>
    </div>

    <script>
        function setCargo(cargo, buttonId) {
            document.getElementById('cargo').value = cargo;

            // Remove a classe "active" de todos os botões de cargo
            document.querySelectorAll('.cargo-button').forEach(button => button.classList.remove('active'));

            // Adiciona a classe "active" ao botão selecionado
            document.getElementById(buttonId).classList.add('active');
        }

        function handleRegister(event) {
            event.preventDefault(); // Impede o envio do formulário
            const activeButton = document.querySelector('.cargo-button.active');
            if (activeButton) {
                const role = activeButton.getAttribute('data-role');
                alert(`Cadastro realizado com sucesso como ${role}!`);
            } else {
                alert('Por favor, selecione uma função.');
            }
        }
    </script>
</body>
</html>
