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

try {
    $objects = $s3Client->listObjects([
        'Bucket' => $s3BucketName
    ]);

    echo "<h1>Files in S3 Bucket</h1>";
    echo "<ul>";
    foreach ($objects['Contents'] as $object) {
        echo "<li><a href='view_file.php?file=" . urlencode($object['Key']) . "'>" . $object['Key'] . "</a></li>";
    }
    echo "</ul>";
} catch (AwsException $e) {
    echo "<p>Error: " . $e->getAwsErrorMessage() . "</p>";
}
?>