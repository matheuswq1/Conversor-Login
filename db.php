<?php
$host = "localhost";  // Endereço do servidor de banco de dados
$user = "root";       // Usuário do banco de dados
$pass = "";           // Senha do banco de dados (geralmente em branco no XAMPP)
$dbname = "login_system"; // Nome do banco de dados que você criou

// Criando a conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
