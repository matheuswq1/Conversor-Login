<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Entre para acessar esta página.";
    header("Location: index.php");
    exit;
}

if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filePath = __DIR__ . '/conversions/' . $file;

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "Erro: arquivo não encontrado.";
    }
} else {
    echo "Erro: nenhuma referência de arquivo fornecida.";
}
