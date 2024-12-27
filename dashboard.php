<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Faça login para acessar esta página.";
    header("Location: index.php");
    exit;
}

$host = 'localhost';
$db = 'login_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM user_files WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Arquivos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Meus Arquivos Convertidos</h1>

    <table>
        <thead>
            <tr>
                <th>Nome Original</th>
                <th>Nome Convertido</th>
                <th>Data</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><?php echo htmlspecialchars($file['original_filename']); ?></td>
                    <td><?php echo htmlspecialchars($file['converted_filename']); ?></td>
                    <td><?php echo htmlspecialchars($file['upload_time']); ?></td>
                    <td><a href="conversions/<?php echo htmlspecialchars($file['converted_filename']); ?>" download>Baixar</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="convert.php" class="btn">Voltar para Conversor</a>
    <a href="logout.php" class="logout-btn">Sair</a>
</body>
</html>
