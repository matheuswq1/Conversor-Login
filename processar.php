<?php
session_start();

// Configuração do banco de dados
$host = 'localhost';
$db = 'login_system';
$user = 'root';
$pass = ''; // Deixe vazio no XAMPP padrão

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['form_type'])) {
        if ($_POST['form_type'] == 'signup') {
            // Lógica de cadastro
        } elseif ($_POST['form_type'] == 'login') {
            // Lógica de login
        }
    }
    
    // Cadastro
    if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Validação de Nome
        if (!preg_match('/^[A-Za-zÀ-ÿ\s]+$/', $name)) {
            $_SESSION['error_message'] = "O nome deve conter apenas letras e espaços.";
            header("Location: index.php");
            exit;
        }

        // Validação de E-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Por favor, insira um e-mail válido.";
            header("Location: index.php"); // Redireciona para o formulário
            exit;
        }

        // Validação de Senha
        if (strlen($password) < 6) {
            $_SESSION['error_message'] = "A senha deve ter pelo menos 6 caracteres.";
            header("Location: index.php");
            exit;
        }

        // Criptografar a senha
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $passwordHash]);

            $_SESSION['success_message'] = "Cadastro realizado com sucesso!";
            header("Location: convert.php");
            exit;
        } catch (PDOException $e) {
            // Verifica se o erro é devido a um e-mail duplicado (violação de chave única)
            if ($e->getCode() == 23000) {
                $_SESSION['error_message'] = "Erro: E-mail já registrado.";
            } else {
                // Caso haja outro tipo de erro, exibe a mensagem do erro
                $_SESSION['error_message'] = "Erro ao registrar: " . htmlspecialchars($e->getMessage());
            }
            // Redireciona para a página inicial para mostrar a mensagem de erro
            header("Location: index.php");
            exit;
        }
        
    }
    // Login
    elseif (isset($_POST['login_email'], $_POST['login_password'])) {
        $email = trim($_POST['login_email']);
        $password = trim($_POST['login_password']);

        // Validação de E-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Por favor, insira um e-mail válido.";
            header("Location: index.php");
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: convert.php");
                exit;
            } else {
                $_SESSION['error_message'] = "E-mail ou senha incorretos.";
                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erro no login: " . $e->getMessage();
            header("Location: index.php");
            exit;
        }
    }
}

header("Location: index.php");
exit;
