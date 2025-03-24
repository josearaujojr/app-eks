<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

header('Content-Type: application/json');

try {
    // Verifica se o arquivo foi enviado corretamente
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Nenhum arquivo foi enviado ou ocorreu um erro no upload.');
    }

    // Configuração do cliente S3
    $s3Client = new S3Client([
        'version' => 'latest',
        'region'  => 'us-east-1', // Altere para sua região
        'credentials' => [
            'key'    => 'SUA_ACCESS_KEY', // Substitua pela sua AWS Access Key
            'secret' => 'SUA_SECRET_KEY', // Substitua pela sua AWS Secret Key
        ],
    ]);

    // Dados do arquivo
    $filePath = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $bucketName = 'SEU_BUCKET'; // Substitua pelo nome do seu bucket

    // Faz o upload para o S3
    $result = $s3Client->putObject([
        'Bucket' => $bucketName,
        'Key'    => $fileName,
        'SourceFile' => $filePath,
    ]);

    // Retorna a URL do arquivo no S3 (opcional)
    $fileUrl = $result->get('ObjectURL');

    echo json_encode([
        'success' => true,
        'message' => 'Arquivo enviado com sucesso!',
        'url'     => $fileUrl,
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage(),
    ]);
}