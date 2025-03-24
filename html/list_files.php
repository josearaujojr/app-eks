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
    <?php include('menu.php'); ?>

    <div class="container">
        <h2>Conteúdo do Bucket S3</h2>

        <?php
            // Desativa warnings de depreciação (opcional para produção)
            error_reporting(E_ALL ^ E_DEPRECATED);

            // Verifica se o SDK está instalado
            if (!file_exists('/var/www/html/vendor/autoload.php')) {
                die('<div class="alert alert-danger">AWS SDK não instalado</div>');
            }

            require '/var/www/html/vendor/autoload.php';

            use Aws\S3\S3Client;
            use Aws\Exception\AwsException;

            // Verifica se SimpleXML está instalado
            if (!extension_loaded('simplexml')) {
                die('<div class="alert alert-danger">Extensão SimpleXML não está instalada</div>');
            }

            // Verifica variáveis de ambiente
            $requiredVars = ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'S3_BUCKET_NAME'];
            foreach ($requiredVars as $var) {
                if (empty(getenv($var))) {
                    die("<div class='alert alert-danger'>Variável $var não está definida</div>");
                }
            }

            try {
                // Configuração do cliente S3
                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region'  => getenv('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key'    => getenv('AWS_ACCESS_KEY_ID'),
                        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
                    ],
                    'http' => [
                        'verify' => false // Apenas para desenvolvimento
                    ]
                ]);

                // Lista os objetos no bucket
                $result = $s3Client->listObjects([
                    'Bucket' => getenv('S3_BUCKET_NAME')
                ]);

                echo '<div class="gallery">';
                
                if (!empty($result['Contents'])) {
                    foreach ($result['Contents'] as $object) {
                        $key = $object['Key'];
                        $presignedUrl = $s3Client->getObjectUrl(
                            getenv('S3_BUCKET_NAME'),
                            $key,
                            '+15 minutes'
                        );
                        
                        echo '<div class="gallery-item">';
                        if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $key)) {
                            echo '<img src="'.htmlspecialchars($presignedUrl).'" alt="'.htmlspecialchars($key).'">';
                        } else {
                            echo '<div class="file-icon"></div>';
                        }
                        echo '<div class="file-name">'.htmlspecialchars($key).'</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert alert-info">O bucket está vazio</div>';
                }
                
                echo '</div>';

            } catch (AwsException $e) {
                echo '<div class="alert alert-danger">Erro ao acessar S3: '.htmlspecialchars($e->getAwsErrorMessage()).'</div>';
            }
        ?>

    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>