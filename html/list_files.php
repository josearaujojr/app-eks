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
        require 'vendor/autoload.php';
        
        use Aws\S3\S3Client;
        use Aws\Exception\AwsException;
        
        try {
            // Configuração do cliente S3
            $s3Client = new S3Client([
                'version' => 'latest',
                'region'  => getenv('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key'    => getenv('AWS_ACCESS_KEY_ID'),
                    'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
                ]
            ]);
            
            $bucket = getenv('S3_BUCKET_NAME');
            
            // Lista objetos no bucket
            $result = $s3Client->listObjects([
                'Bucket' => $bucket
            ]);
            
            echo '<div class="gallery">';
            
            if (!empty($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $key = $object['Key'];
                    $ext = strtolower(pathinfo($key, PATHINFO_EXTENSION));
                    
                    // Gera URL pré-assinada
                    $cmd = $s3Client->getCommand('GetObject', [
                        'Bucket' => $bucket,
                        'Key'    => $key
                    ]);
                    
                    $request = $s3Client->createPresignedRequest($cmd, '+15 minutes');
                    $presignedUrl = (string)$request->getUri();
                    
                    echo '<div class="gallery-item">';
                    
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        echo '<img src="'.$presignedUrl.'" alt="'.$key.'">';
                    } else {
                        echo '<div style="height:150px;display:flex;align-items:center;justify-content:center;">';
                        echo '<span class="glyphicon glyphicon-file" style="font-size:50px;"></span>';
                        echo '</div>';
                    }
                    
                    echo '<div class="file-name">'.$key.'</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="alert alert-info">O bucket está vazio</div>';
            }
            
            echo '</div>';
            
        } catch (AwsException $e) {
            echo '<div class="alert alert-danger">Erro ao acessar S3: '.$e->getAwsErrorMessage().'</div>';
        }
        ?>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>