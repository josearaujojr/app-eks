<!DOCTYPE html>
<html>
<head>
    <title>Visualizador S3</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        .gallery-item {
            width: 200px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background: white;
        }
        .gallery-item img {
            max-width: 100%;
            height: 150px;
            object-fit: contain;
        }
        .file-name {
            word-break: break-all;
            margin-top: 10px;
            font-size: 12px;
        }
        .alert {
            margin: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">HOME</a>
                <a class="navbar-brand" href="rds.php">RDS</a>
                <a class="navbar-brand" href="list_files.php">S3</a>
                <a class="navbar-brand" href="imagem.php">IMAGEM</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Conteúdo do Bucket S3</h2>

        <?php
            // Ativa exibição de erros
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // Verifica se o SDK está instalado
            if (!file_exists('/var/www/html/vendor/autoload.php')) {
                die('<div class="alert alert-danger">AWS SDK não instalado. Execute: composer require aws/aws-sdk-php</div>');
            }

            require '/var/www/html/vendor/autoload.php';

            use Aws\S3\S3Client;
            use Aws\Exception\AwsException;

            // Verifica variáveis de ambiente
            $requiredVars = ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'S3_BUCKET_NAME'];
            foreach ($requiredVars as $var) {
                if (empty(getenv($var))) {
                    die("<div class='alert alert-danger'>Variável $var não está definida</div>");
                }
            }

            try {
                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region'  => getenv('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key'    => getenv('AWS_ACCESS_KEY_ID'),
                        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
                    ],
                    'http' => [
                        'verify' => false // Desativa verificação SSL se necessário
                    ]
                ]);

                // DEBUG: Verifica conexão com o S3
                $buckets = $s3Client->listBuckets();
                echo "<!-- Conexão com S3 bem-sucedida. Buckets disponíveis: " . json_encode($buckets['Buckets']) . " -->";

                $result = $s3Client->listObjects([
                    'Bucket' => getenv('S3_BUCKET_NAME')
                ]);

                // Restante do código para exibir as imagens...
                
            } catch (AwsException $e) {
                echo "<div class='alert alert-danger'>ERRO S3: " . $e->getAwsErrorMessage() . "</div>";
                echo "<!-- Detalhes do erro: " . $e->getMessage() . " -->";
            }
        ?>

    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>