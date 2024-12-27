<?php
    session_start();

    // Verifica se o usuário está logado
    if (isset($_SESSION['user_id'])) {
        // Destroi a sessão para encerrar o login
        session_destroy();
        $logout_message = "Você saiu do sistema com sucesso.";
    } else {
        $logout_message = "Você não estava logado.";
    }

    // Destruir a sessão e limpar todas as variáveis de sessão
    session_unset();
    session_destroy();

    // Redirecionar para a página de login (ou outra página)
    header("Location: index.php");
    exit;

    // Verifica se o usuário está logado
    if (isset($_SESSION['user_id'])) {
        // Exibe o botão de sair se o usuário estiver logado
        echo '<a href="logout.php" class="logout-btn">Sair</a>';
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f3f4f6;
            color: #333;
        }

        .message {
            font-size: 20px;
            margin-bottom: 30px;
        }

        a {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Logout</h1>
    <p class="message"><?php echo htmlspecialchars($logout_message); ?></p>
    <a href="index.php">Voltar para a Página Inicial</a>
</body>

</html>
