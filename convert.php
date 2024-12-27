<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Entre para acessar esta página.";
    header("Location: index.php");
    exit;
}

// Configuração do banco de dados
$host = 'localhost';
$db = 'login_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

function cleanOldFiles($directory, $lifetime = 3600) {
    if (is_dir($directory)) {
        $files = scandir($directory);
        $now = time();

        foreach ($files as $file) {
            $filePath = $directory . '/' . $file;

            // Ignorar diretórios e arquivos ocultos como "." e ".."
            if (is_file($filePath) && $file[0] !== '.') {
                $fileCreationTime = filemtime($filePath);

                // Verifica se o arquivo tem mais de $lifetime segundos
                if (($now - $fileCreationTime) > $lifetime) {
                    unlink($filePath); // Remove o arquivo
                }
            }
        }
    }
}
// Configuração dos diretórios de upload e conversão
$uploadDir = __DIR__ . '/uploads/';
$conversionDir = __DIR__ . '/conversions/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($conversionDir)) mkdir($conversionDir, 0777, true);
cleanOldFiles($uploadDir, 3600); // Limpa arquivos antigos no diretório de uploads
cleanOldFiles($conversionDir, 3600); // Limpa arquivos antigos no diretório de conversões


$message = '';
$convertedFiles = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $audioQuality = isset($_POST['quality']) ? $_POST['quality'] : '192k'; // Padrão é 192k

    // Percorrer os arquivos enviados
    foreach ($_FILES['video']['tmp_name'] as $index => $tmpName) {
        $video = $_FILES['video'];
        $fileName = $video['name'][$index];
        $fileTmpName = $video['tmp_name'][$index];
        $fileError = $video['error'][$index];

        if ($fileError !== UPLOAD_ERR_OK) {
            $message .= 'Erro ao fazer upload do arquivo: ' . $fileName . '.<br>';
            continue;
        } else {
            $allowedExtensions = ['mp4'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                $message .= 'Formato de arquivo inválido: ' . $fileName . '. Por favor, envie um arquivo MP4.<br>';
                continue;
            } else {
                $uploadedFilePath = $uploadDir . basename($fileName);
                
                if (move_uploaded_file($fileTmpName, $uploadedFilePath)) {

                    // Comando para verificar se o vídeo contém áudio
                    $ffmpegCheckAudioCommand = "ffmpeg -i " . escapeshellarg($uploadedFilePath) . " 2>&1";
                    exec($ffmpegCheckAudioCommand, $output, $returnVar);

                    // Verifica se o arquivo contém stream de áudio
                    $hasAudio = false;
                    foreach ($output as $line) {
                        if (strpos($line, 'Audio:') !== false) {
                            $hasAudio = true;
                            break;
                        }
                    }

                    if (!$hasAudio) {
                        $message .= 'O arquivo ' . $fileName . ' não contém áudio. Por favor, envie um vídeo que tenha áudio.<br>';
                    } else {
                        $convertedFilePath = $conversionDir . pathinfo($fileName, PATHINFO_FILENAME) . '.mp3';

                        // Capturar o tempo de início
                        $startTime = microtime(true);

                        // Comando para conversão com FFmpeg e qualidade selecionada
                        $ffmpegCommand = "ffmpeg -i " . escapeshellarg($uploadedFilePath) . " -vn -acodec libmp3lame -ab " . escapeshellarg($audioQuality) . " " . escapeshellarg($convertedFilePath) . " 2>&1";
                        exec($ffmpegCommand, $output, $returnVar);

                        // Capturar o tempo de término
                        $endTime = microtime(true);

                        // Calcular o tempo de conversão
                        $conversionTime = $endTime - $startTime;

                        $outputLog = implode("\n", $output);

                        if ($returnVar === 0) {
                            $convertedFiles[] = [
                                'fileName' => $fileName,
                                'convertedFilePath' => pathinfo($fileName, PATHINFO_FILENAME) . '.mp3',
                                'conversionTime' => round($conversionTime, 2)
                            ];
                        } else {
                            $message .= 'Erro ao converter o arquivo ' . $fileName . '. Detalhes: <pre>' . htmlspecialchars($outputLog) . '</pre><br>';
                        }
                    }
                } else {
                    $message .= 'Erro ao mover o arquivo ' . $fileName . ' para o diretório de upload.<br>';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/styles_conversor.css">
    <title>Conversor MP4 para MP3</title>
    <style>
        .drag-area {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
        }
        .drag-area.dragover {
            background-color: #f9f9f9;
            border-color: #6c6cff;
        }
    </style>
</head>
<body>
    <h1>Conversor MP4 para MP3</h1>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form id="uploadForm" action="convert.php" method="POST" enctype="multipart/form-data">
        <div class="drag-area" id="dragArea">
            <p>Arraste e solte os arquivos aqui ou clique para selecionar</p>
            <input type="file" name="video[]" id="fileInput" accept=".mp4" multiple style="display: none;">
        </div>
        <label for="quality">Escolha a qualidade do áudio:</label>
        <select name="quality" id="quality">
            <option value="128k">Baixa (128 kbps)</option>
            <option value="192k" selected>Média (192 kbps)</option>
            <option value="256k">Alta (256 kbps)</option>
            <option value="320k">Muito Alta (320 kbps)</option>
        </select><br><br>
        <button type="submit">Converter</button>
    </form>

    <?php if (!empty($convertedFiles)): ?>
        <div class="converted-files">
            <h2>Arquivos Convertidos</h2>
            <?php foreach ($convertedFiles as $file): ?>
                <div class="file-item">
                    <img src="https://img.icons8.com/ios/452/mp3.png" alt="Audio Icon">
                    <div>
                        <strong><?php echo pathinfo($file['fileName'], PATHINFO_FILENAME); ?>.mp3</strong><br>
                        Tempo de conversão: <?php echo $file['conversionTime']; ?> segundos<br>
                        <a href="download.php?file=<?php echo urlencode($file['convertedFilePath']); ?>">
                            <button>Baixar MP3</button>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="logout.php" class="logout-btn">Sair</a>
    <script>
        const dragArea = document.getElementById('dragArea');
        const fileInput = document.getElementById('fileInput');

        // Quando o usuário clica na área de arrastar
        dragArea.addEventListener('click', () => fileInput.click());

        // Quando o usuário arrasta arquivos para a área
        dragArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dragArea.classList.add('dragover');
        });

        // Quando o usuário sai da área de arrastar
        dragArea.addEventListener('dragleave', () => {
            dragArea.classList.remove('dragover');
        });

        // Quando o usuário solta arquivos na área
        dragArea.addEventListener('drop', (event) => {
            event.preventDefault();
            dragArea.classList.remove('dragover');

            // Adicionar arquivos soltos ao input
            fileInput.files = event.dataTransfer.files;

            // Mostrar mensagem ou lista de arquivos (opcional)
            const fileNames = Array.from(fileInput.files).map(file => file.name).join(', ');
            dragArea.innerHTML = `<p>Arquivos selecionados: ${fileNames}</p>`;
        });
    </script>
</body>
</html>