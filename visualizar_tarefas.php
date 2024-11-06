<?php
require_once 'conexao.php';

// Verifica se houve erro de conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Alterar status da tarefa
if (isset($_POST['update_status'])) {
    $tarefa_cod = $_POST['tarefa_cod'];
    $status = $_POST['status'];

    if (!empty($tarefa_cod) && !empty($status)) {
        $sql = "UPDATE tarefas SET tarefa_status = ? WHERE tarefa_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $tarefa_cod);

        if ($stmt->execute()) {
            echo "<p>Status da tarefa atualizado com sucesso!</p>";
        } else {
            echo "<p>Erro ao atualizar status: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Consultar as tarefas para exibir na tabela
$tarefas = $conn->query("SELECT * FROM tarefas");
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Tarefas</title>
    <style>
        /* Reset e configurações gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #444;
            font-size: 2em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        td {
            background-color: #f9f9f9;
        }

        /* Estilo das ações */
        .actions form {
            display: inline-block;
            text-align: center;
        }

        .actions select, .actions button {
            padding: 8px 12px;
            margin-top: 10px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .actions select {
            background-color: #fff;
            cursor: pointer;
        }

        .actions button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .actions button:hover {
            background-color: #218838;
        }

        .actions button:focus {
            outline: none;
        }

        /* Estilo para mensagens */
        .message {
            text-align: center;
            font-size: 1.2em;
            margin-top: 20px;
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 4px;
            display: none;
        }

        /* Estilo responsivo para telas pequenas */
        @media screen and (max-width: 768px) {
            .container {
                padding: 15px;
            }

            h2 {
                font-size: 1.6em;
            }

            table, th, td {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Visualizar Tarefas</h2>

        <!-- Lista de Tarefas -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Setor</th>
                    <th>Prioridade</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tarefa = $tarefas->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($tarefa['tarefa_cod']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_setor']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_prioridade']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_descricao']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_status']) ?></td>
                        <td class="actions">
                            <!-- Formulário para alterar o status -->
                            <form method="POST">
                                <input type="hidden" name="tarefa_cod" value="<?= htmlspecialchars($tarefa['tarefa_cod']) ?>">
                                
                                <label for="status">Novo Status:</label>
                                <select name="status" required>
                                    <option value="Em andamento" <?= $tarefa['tarefa_status'] == 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                                    <option value="Concluída" <?= $tarefa['tarefa_status'] == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                                    <option value="Pendente" <?= $tarefa['tarefa_status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                </select>

                                <button type="submit" name="update_status">Alterar Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
