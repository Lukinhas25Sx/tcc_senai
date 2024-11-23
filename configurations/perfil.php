<?php
// Inclui a conexão e a proteção de sessão
include 'conection.php';
include 'protect.php';
include 'header.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../cadastro/index.php");
    exit();
}

// Busca os dados do usuário atual
$query_user = "SELECT nome, foto_perfil FROM users WHERE id = :id";
$stmt_user = $pdo->prepare($query_user);
$stmt_user->execute(['id' => $_SESSION['id']]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Processa as alterações de perfil
    $nome = $_POST['nome'];
    $nova_senha = $_POST['nova_senha'];

    // Upload da foto de perfil se uma nova foto for enviada
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
        $foto_perfil = file_get_contents($_FILES['foto_perfil']['tmp_name']); // Obtém o conteúdo binário da imagem
    } else {
        $foto_perfil = $user['foto_perfil']; // Mantém a imagem atual se não houver nova imagem
    }

    // Atualização no banco de dados
    $update_query = "UPDATE users SET nome = :nome, foto_perfil = :foto_perfil";
    $params = ['nome' => $nome, 'foto_perfil' => $foto_perfil, 'id' => $_SESSION['id']];

    // Se uma nova senha for definida
    if (!empty($nova_senha)) {
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $update_query .= ", senha = :senha";
        $params['senha'] = $nova_senha_hash;
        header("Location: " . $_SERVER['PHP_SELF']);
    }

    $update_query .= " WHERE id = :id";
    $stmt_update = $pdo->prepare($update_query);
    
    if ($stmt_update->execute($params)) {
        echo "Perfil atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar o perfil: " . $stmt_update->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
<style>
    /* Estilo básico do corpo da página */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fa;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Cabeçalho fixo (caso tenha um header) */
.with-header {
    padding-top: 70px; /* Ajuste do espaço no topo */
}

/* Estilo do título */
h2 {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
}

/* Formulário de edição de perfil */
form {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

/* Estilo para os campos do formulário */
label {
    font-size: 16px;
    margin-bottom: 8px;
    display: block;
    color: #555;
}

/* Estilo dos inputs de texto e senha */
input[type="text"],
input[type="password"],
input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
}

/* Estilo do botão de envio */
button[type="submit"] {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Estilo da imagem de perfil */
img {
    border-radius: 50%;
    margin-bottom: 15px;
}

/* Container da foto de perfil */
input[type="file"] {
    margin-bottom: 20px;
}

/* Adicionando uma borda nas imagens */
img {
    border: 2px solid #ddd;
    box-sizing: border-box;
}

/* Melhorando a aparência do formulário ao passar o mouse sobre o campo de entrada */
input[type="text"]:focus,
input[type="password"]:focus {
    border-color: #007bff;
    outline: none;
}

/* Estilo de mensagem de sucesso ou erro */
.success-message {
    color: green;
    font-size: 16px;
    text-align: center;
}

.error-message {
    color: red;
    font-size: 16px;
    text-align: center;
}
/* Estilo para o botão personalizado */
.custom-file-upload {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: block; /* Faz o botão ocupar toda a largura disponível */
    font-size: 16px;
    margin-bottom: 10px;
    width: 100%;
    box-sizing: border-box;
    text-align: ;
}


.custom-file-upload:hover {
    background-color: #0056b3;
}

</style>
</head>
<body class="with-header" style="padding-top: 70px;">
    <h2>Perfil do Usuário</h2>
    <form method="POST" enctype="multipart/form-data">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
    <label>Nova Senha:</label>
    <input type="password" name="nova_senha" placeholder="Deixe em branco para manter a senha atual">
    <label>Foto de Perfil:</label>
    <?php if (!empty($user['foto_perfil'])) : ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['foto_perfil']); ?>" alt="Foto de perfil" width="100" style="border-radius: 50%;">
    <?php endif; ?>
    <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" style="display: none;">
    <label for="foto_perfil" class="custom-file-upload">Escolher arquivo</label>
    <button type="submit">Atualizar Perfil</button>
</form>

</body>
</html>