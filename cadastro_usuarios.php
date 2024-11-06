<?php
require_once 'conexao.php';

// Verificar se a conexão com o banco de dados foi bem-sucedida
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Adicionar usuário
if (isset($_POST['add_user'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (!empty($nome) && !empty($email)) {
        $sql = "INSERT INTO usuarios (usu_nome, usu_email) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome, $email);

        if ($stmt->execute()) {
            echo "<p>Usuário cadastrado com sucesso!</p>";
        } else {
            echo "<p>Erro ao cadastrar usuário: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Editar usuário
if (isset($_POST["edit_user"])) {
    $usu_cod = $_POST['usu_cod'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (!empty($usu_cod) && !empty($nome) && !empty($email)) {
        $sql = "UPDATE usuarios SET usu_nome = ?, usu_email = ? WHERE usu_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $email, $usu_cod);

        if ($stmt->execute()) {
            echo "<p>Usuário atualizado com sucesso!</p>";
        } else {
            echo "<p>Erro ao atualizar usuário: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Excluir usuário
if (isset($_POST["delete_user"])) {
    $usu_cod = $_POST['usu_cod'];

    if (!empty($usu_cod)) {
        $sql = "DELETE FROM usuarios WHERE usu_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usu_cod);

        if ($stmt->execute()) {
            echo "<p>Usuário excluído com sucesso!</p>";
        } else {
            echo "<p>Erro ao excluir usuário: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Consultar os usuários para exibir na tabela
$usuarios = $conn->query("SELECT * FROM usuarios");
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif; 
            background-color: #f0f2f5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            color: #333; 
        }
        .container { 
            width: 100%; 
            max-width: 800px; 
            background-color: #fff; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); 
            transition: box-shadow 0.3s ease; 
        }
        .container:hover { 
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); 
        }
        h2 { 
            text-align: center; 
            color: #007bff; 
            margin-bottom: 30px; 
            font-size: 24px; 
        }
        form { 
            display: flex; 
            flex-direction: column; 
            gap: 15px; 
        }
        label { 
            font-weight: bold; 
            color: #555; 
        }
        input[type="text"], input[type="email"], input[type="number"] { 
            padding: 12px; 
            border: 2px solid #ddd; 
            border-radius: 5px; 
            font-size: 16px; 
            transition: border-color 0.3s; 
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus { 
            border-color: #007bff; 
        }
        button { 
            padding: 12px 18px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background-color 0.3s ease; 
        }
        button:hover { 
            background-color: #0056b3; 
        }
        table { 
            width: 100%; 
            margin-top: 30px; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 15px; 
            text-align: center; 
            border: 1px solid #ddd; 
        }
        th { 
            background-color: #007bff; 
            color: white; 
        }
        .actions { 
            display: flex; 
            justify-content: center; 
            gap: 10px; 
        }
        .actions form { 
            display: inline-block; 
        }
        .actions input[type="text"], .actions input[type="email"] { 
            width: 150px; 
            margin-right: 10px; 
        }
        .message { 
            text-align: center; 
            color: #28a745; 
            margin-bottom: 15px; 
        }
        .message.error { 
            color: #dc3545; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Usuários</h2>

        <!-- Mensagem de sucesso/erro -->
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <!-- Formulário para Adicionar Usuário -->
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <button type="submit" name="add_user">Cadastrar</button>
        </form>

        <!-- Lista de Usuários -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['usu_cod']) ?></td>
                        <td><?= htmlspecialchars($usuario['usu_nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['usu_email']) ?></td>
                        <td class="actions">
                            <!-- Formulário para Editar Usuário -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="usu_cod" value="<?= htmlspecialchars($usuario['usu_cod']) ?>">
                                <input type="text" name="nome" value="<?= htmlspecialchars($usuario['usu_nome']) ?>" required>
                                <input type="email" name="email" value="<?= htmlspecialchars($usuario['usu_email']) ?>" required>
                                <button type="submit" name="edit_user">Editar</button>
                            </form>

                            <!-- Formulário para Excluir Usuário -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="usu_cod" value="<?= htmlspecialchars($usuario['usu_cod']) ?>">
                                <button type="submit" name="delete_user" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
