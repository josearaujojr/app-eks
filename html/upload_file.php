<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$awsAccessKeyId = getenv('AWS_ACCESS_KEY_ID');
$awsSecretAccessKey = getenv('AWS_SECRET_ACCESS_KEY');
$awsDefaultRegion = getenv('AWS_DEFAULT_REGION');
$s3BucketName = getenv('S3_BUCKET_NAME');

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $awsDefaultRegion,
    'credentials' => [
        'key'    => $awsAccessKeyId,
        'secret' => $awsSecretAccessKey,
    ]
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $fileTmpName = $file['tmp_name'];

    try {
        $result = $s3Client->putObject([
            'Bucket' => $s3BucketName,
            'Key'    => $fileName,
            'SourceFile' => $fileTmpName,
            'ACL'    => 'public-read'
        ]);

        echo "<p>File uploaded successfully.</p>";
        echo "<img src='" . $result['ObjectURL'] . "' alt='Uploaded Image'>";
    } catch (AwsException $e) {
        echo "<p>Error: " . $e->getAwsErrorMessage() . "</p>";
    }
}
?>

<h1>Upload Image to S3</h1>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>