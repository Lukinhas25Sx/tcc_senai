<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cadastro.css">
    <title>Cadastrar Usuário</title>
    <style>
        .cargo-buttons {
            display: flex;
            gap: 10px; /* Espaçamento entre os botões */
            margin: 10px 0; /* Margem acima e abaixo dos botões */
        }
        .cargo-button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #002060; /* Cor do botão */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .cargo-button:hover {
            background-color: #001d4d; /* Cor ao passar o mouse */
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cadastro de Usuário</h1>
    </div>
    <div class="container">
        <form class="login-form" action="/tcc_senai/configurations/process.php" method="post">
            <h2>Preencha seus dados</h2>
            <p>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </p>
            <p>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </p>
            <p>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </p>
            <p>
                <label>Cargo:</label>
                <div class="cargo-buttons">
                    <button type="button" class="cargo-button" onclick="setCargo('professor')">Professor</button>
                    <button type="button" class="cargo-button" onclick="setCargo('manutencao')">Manutenção</button>
                </div>
                <input type="hidden" id="cargo" name="cargo" required>
            </p>
            <p>
                <button type="submit">Enviar</button>
            </p>
            <p>Já tem uma conta? <a href="index.php">Faça login aqui</a></p>
        </form>
    </div>

    <script>
        function setCargo(cargo) {
            document.getElementById('cargo').value = cargo; // Define o valor do campo oculto
        }
    </script>
</body>
</html>
