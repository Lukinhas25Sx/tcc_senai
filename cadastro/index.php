<?php 
include ('../configurations/conexions.php')
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="/enviar" method="post">
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
            <label for="cargo">Cargo:</label>
            <select id="cargo" name="cargo" required>
                <option value="">Selecione</option>
                <option value="professor">Professor</option>
                <option value="manutencao">Manutenção</option>
            </select>
        </p>
        <p>
            <button type="submit">Enviar</button>
        </p>
    </form>
</body>
</html>