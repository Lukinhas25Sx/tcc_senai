<?php
// Inclua a conexão e verifique a sessão
include 'conection.php';
include 'protect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../cadastro/index.php");
    exit();
}

// Consulta o nome e a foto de perfil do usuário logado
$query_user = "SELECT nome, foto_perfil FROM users WHERE id = :id";
$stmt_user = $pdo->prepare($query_user);
$stmt_user->execute(['id' => $_SESSION['id']]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página com Topo</title>
    <link rel="stylesheet" href="header.css">
    <style>
        /* Estilo da barra superior */
        /* Estilo da barra superior */
    .top-bar {
        width: 100%;
        background-color: #002366; /* Azul escuro */
        padding: 5px 20px;
        color: white;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        height: 60px; /* Ajusta a altura da barra superior */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }

    /* Estilo do container de informações do usuário */
    .user-info {
        display: flex;
        align-items: center;
    }

    /* Estilo da imagem de perfil */
    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%; /* Forma redonda */
        margin-right: 10px;
        object-fit: cover; /* Garante que a imagem se ajuste ao container */
        border: 2px solid white; /* Contorno branco */
    }

    /* Estilo do nome do usuário */
    .username {
        font-size: 1rem;
        color: white;
        font-weight: bold;
    }

    /* Adiciona espaço para o conteúdo abaixo da barra superior fixa */
    body {
        margin-top: 60px;
    }

    </style>
</head>
<body>
    <div class="top-bar">
        <div class="user-info">
            <?php if (!empty($user['foto_perfil'])) : ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($user['foto_perfil']); ?>" alt="Foto de perfil" class="profile-pic">
            <?php endif; ?>
            <span class="username"><?php echo htmlspecialchars($user['nome']); ?></span>
        </div>
    </div>
</body>
</html>

