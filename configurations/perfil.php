<?php
// Inclui a conexão e a proteção de sessão
include 'conection.php';
include 'protect.php';

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
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
        <input type="file" name="foto_perfil" accept="image/*">

        <button type="submit">Atualizar Perfil</button>
    </form>
</body>
</html>

