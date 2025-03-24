<!DOCTYPE html>
<html>
<head>
    <title>AWS Cloud Practitioner Essentials</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
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
            height: auto;
        }
        .file-link {
            display: block;
            margin-top: 10px;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php include('menu.php'); ?>

                <div class="container" style="width: 100%; border-radius: 3px; background-color:#eee; margin-top: 20px; color:#fff; padding: 20px;">
                    <h2>Conteúdo do Bucket S3</h2>
                    
                    <?php
                    require 'vendor/autoload.php';

                    use Aws\S3\S3Client;
                    use Aws\Exception\AwsException;

                    // Recupera credenciais das variáveis de ambiente
                    $awsAccessKeyId = getenv('AWS_ACCESS_KEY_ID');
                    $awsSecretAccessKey = getenv('AWS_SECRET_ACCESS_KEY');
                    $awsDefaultRegion = getenv('AWS_DEFAULT_REGION');
                    $s3BucketName = getenv('S3_BUCKET_NAME');

                    // Verifica se as credenciais estão disponíveis
                    if (empty($awsAccessKeyId) || empty($awsSecretAccessKey) || empty($awsDefaultRegion) || empty($s3BucketName)) {
                        die("<div class='alert alert-danger'>Erro: Credenciais do S3 não configuradas corretamente.</div>");
                    }

                    try {
                        // Cria cliente S3
                        $s3Client = new S3Client([
                            'version' => 'latest',
                            'region'  => $awsDefaultRegion,
                            'credentials' => [
                                'key'    => $awsAccessKeyId,
                                'secret' => $awsSecretAccessKey,
                            ]
                        ]);

                        // Lista objetos no bucket
                        $objects = $s3Client->listObjects([
                            'Bucket' => $s3BucketName
                        ]);

                        // Filtra apenas imagens (opcional)
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        echo "<div class='gallery'>";
                        
                        foreach ($objects['Contents'] as $object) {
                            $fileKey = $object['Key'];
                            $extension = strtolower(pathinfo($fileKey, PATHINFO_EXTENSION));
                            
                            // Gera URL pré-assinada válida por 15 minutos
                            $cmd = $s3Client->getCommand('GetObject', [
                                'Bucket' => $s3BucketName,
                                'Key'    => $fileKey
                            ]);
                            
                            $request = $s3Client->createPresignedRequest($cmd, '+15 minutes');
                            $presignedUrl = (string)$request->getUri();

                            if (in_array($extension, $imageExtensions)) {
                                // Exibe miniaturas para imagens
                                echo "<div class='gallery-item'>";
                                echo "<img src='".htmlspecialchars($presignedUrl)."' alt='".htmlspecialchars($fileKey)."'>";
                                echo "<a href='".htmlspecialchars($presignedUrl)."' target='_blank' class='file-link'>".htmlspecialchars($fileKey)."</a>";
                                echo "</div>";
                            } else {
                                // Exibe apenas link para outros tipos de arquivo
                                echo "<div class='gallery-item'>";
                                echo "<div style='height: 150px; display: flex; align-items: center; justify-content: center;'>";
                                echo "<i class='glyphicon glyphicon-file' style='font-size: 48px;'></i>";
                                echo "</div>";
                                echo "<a href='".htmlspecialchars($presignedUrl)."' target='_blank' class='file-link'>".htmlspecialchars($fileKey)."</a>";
                                echo "</div>";
                            }
                        }
                        
                        echo "</div>";

                    } catch (AwsException $e) {
                        echo "<div class='alert alert-danger'>Erro ao acessar o S3: ".htmlspecialchars($e->getAwsErrorMessage())."</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>