<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Usuário já está logado, redireciona para o dashboard
    header("Location: convert.php");
    exit;
}
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
    <title>Login Page</title>
</head>

<body>

    <div class="container" id="container">


        <!-- Formulário de Cadastro -->
        <div class="form-container sign-up">
            <form method="POST" action="processar.php" id="signupForm">
                <h1>Criar Conta</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>ou use seu email para cadastro</span>
                
                <!-- Nome: Aceitar apenas letras -->
                <input type="text" name="name" id="name" placeholder="Nome" 
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                    required pattern="[A-Za-zÀ-ÿ\s]+" 
                    title="O nome deve conter apenas letras e espaços.">
                
                <!-- E-mail: Verificar formato -->
                <input type="email" name="email" id="email" placeholder="Email" 
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                required>


                
                <!-- Senha -->
                <input type="password" name="password" id="password" placeholder="Senha" required>
                <!-- Ícone de olho dentro do input -->
                <span id="toggle-password" class="eye-icon">
                    <i class="fa-solid fa-eye"></i>
                </span>

                <!-- Mensagem de erro abaixo do campo de senha -->
                <?php if (!empty($error_message)): ?>
                    <span class="error-message" style="color: blue; font-size: 12px;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </span>
                <?php endif; ?>
                
                <button type="submit">Inscrever</button>
            </form>
        </div>

        <!-- Formulário de Login -->
        <div class="form-container sign-in">
            <form method="POST" action="processar.php">
                <img class="Logo" src="img/Logo_Matheus_CodeV2.png" alt="Logo_Matheus_Code" width="200px">
                <h1>Entrar</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>ou use sua senha de email</span>
                <input type="email" name="login_email" placeholder="Email" required>
                <input type="password" name="login_password" placeholder="Senha" required>
                
                <!-- Mensagem de erro abaixo do campo de senha -->
                <?php if (!empty($error_message)): ?>
                    <span class="error-message" style="color: blue; font-size: 12px;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </span>
                <?php endif; ?>

                <a href="#" class="esqueceu_senha">Esqueceu sua senha?</a>
                <button type="submit">Entrar</button>
            </form>
        </div>

        <!-- Toggle Container -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bem vindo de volta!</h1>
                    <p>Insira seus dados pessoais para usar todos os recursos do site</p>
                    <button class="hidden" id="login">Entrar</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Olá, amigo!</h1>
                    <p>Registre-se com seus dados pessoais para usar todos os recursos do site</p>
                    <button class="hidden" id="register">Inscrever</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay do Pop-up -->
    <div id="popup-overlay"></div>

    <!-- Pop-up -->
    <div id="popup">
        <h2>Desenvolvido com:</h2>
        <p>HTML, CSS, JavaScript e PHP</p>
        <button id="close-btn">Fechar</button>
    </div>

    <!-- Rodapé com Copyright -->
    <footer>
        <p>&copy; 2025 Matheus Code. Todos os direitos reservados.</p>
    </footer>

</body>

</html>