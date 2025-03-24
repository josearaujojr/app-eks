<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

header('Content-Type: application/json');

try {
    // Verifica se o arquivo foi enviado (corrigido o nome do campo)
    if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Nenhum arquivo foi enviado ou ocorreu um erro no upload.');
    }

    $file = $_FILES['fileToUpload'];

    // Validações básicas
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no upload do arquivo.');
    }

    if ($file['size'] > 10 * 1024 * 1024) { // 10MB max
        throw new Exception('O arquivo é muito grande. Tamanho máximo: 10MB');
    }

    // Configura o cliente S3
    $s3Client = new S3Client([
        'version' => 'latest',
        'region'  => getenv('AWS_DEFAULT_REGION'),
        'credentials' => [
            'key'    => getenv('AWS_ACCESS_KEY_ID'),
            'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
        ]
    ]);

    // Gera um nome único para o arquivo
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExtension;
    $bucket = getenv('S3_BUCKET_NAME');

    // Faz o upload para o S3
    $result = $s3Client->putObject([
        'Bucket' => $bucket,
        'Key'    => 'uploads/' . $fileName,
        'SourceFile' => $file['tmp_name'],
        'ACL'    => 'public-read',
        'ContentType' => mime_content_type($file['tmp_name'])
    ]);

    // Retorna a URL pública do arquivo
    echo json_encode([
        'success' => true,
        'message' => 'Arquivo enviado com sucesso!',
        'url' => $result['ObjectURL']
    ], JSON_UNESCAPED_SLASHES);

} catch (S3Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro no S3: ' . $e->getAwsErrorMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>