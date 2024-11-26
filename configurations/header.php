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
    <style>
        /* Estilos da barra superior isolados com ID */
        #top-bar {
            font-family: Georgia, 'Times New Roman', Times, serif;
            width: 100%;
            background-color: #002060;
            padding: 8px 20px;
            color: white;
            display: flex;
            justify-content: space-between; /* Modificado para distribuir o conteúdo */
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            height: 60px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        /* Ajusta o padding-top apenas quando o body tem a classe 'with-header' */
        body.with-header {
            padding-top: 60px;
        }

        /* Estilo do link do usuário */
        #top-bar .user-info {
            display: flex;
            align-items: center;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }

        #top-bar .user-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit; /* Garante que herda a cor branca */
        }

        #top-bar .user-link:hover {
            text-decoration: underline; /* Efeito de hover apenas para a barra superior */
        }

        /* Estilo da imagem de perfil */
        #top-bar .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 2px solid white;
        }

        /* Estilo do nome do usuário */
        #top-bar .username {
            font-size: 1rem;
            color: white;
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
            margin-right: 40px;
        }

        /* Estilo do botão de logout */
        #top-bar .logout-btn {
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none; /* Para garantir que o botão não tenha sublinhado */
        }

        #top-bar .logout-btn:hover {
            background-color: #a80000; /* Efeito de hover */
        }
        .inicial{
            display: block;
            font-size: 2em;
            margin-block-start: 0.67em;
            margin-block-end: 0.67em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
            unicode-bidi: isolate;
            color: inherit; /* Remove a cor azul */
            text-decoration: none; /* Remove o sublinhado */
            margin-right: 80px;
            margin-top: 23px;
        }
    </style>
</head>
<body>
    <div id="top-bar">
        <!-- Botão de logout no lado esquerdo -->
         <div class="logoutereserva">
         <a href="../configurations/logout.php" class="logout-btn">Sair</a>
         <a href="../ProfArea/profarea.php" style="padding: 10px; color: white; text-decoration: none; border-radius: 5px;">Envie sua mensagem</a>

         </div>

         <a href="../reservas/reservas.php" class="inicial">Edu Organiza</a>

        <!-- Informações do usuário no lado direito -->
        <div class="user-info">
            <a href="../configurations/perfil.php" class="user-link">
                <?php if (!empty($user['foto_perfil'])) : ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($user['foto_perfil']); ?>" alt="Foto de perfil" class="profile-pic">
                <?php endif; ?>
                <span class="username"><?php echo htmlspecialchars($user['nome']); ?></span>
            </a>
        </div>
    </div>
</body>
</html>
