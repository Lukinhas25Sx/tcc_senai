<?php 
session_start(); 
include '../configurations/conection.php'; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edu Organiza - Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="header">
        <h1>Edu Organiza</h1>
    </div>
    <div class="container">
        <div class="login-form">
            <h2>Login</h2>

            <?php if (isset($_SESSION['error_message'])): ?>
                <p class="error-message"><?php echo $_SESSION['error_message']; ?></p>
                <?php unset($_SESSION['error_message']); // Remove a mensagem após exibi-la ?>
            <?php endif; ?>

            <form action="../configurations/login_process.php" method="post">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Seu email" required>
                
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
                
                <button type="submit">Entrar</button>
            </form>
            <p>Ainda não tem uma conta? <a href="signup.php">Cadastre-se aqui</a></p>
        </div>
    </div>
</body>
</html>
