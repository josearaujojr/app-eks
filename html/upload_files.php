<!DOCTYPE html>
<html>
<head>
    <title>AWS Cloud Practitioner Essentials</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php include('menu.php'); ?>

                <div class="container" style="width: 100%; border-radius: 3px; background-color:#eee; margin-top: 20px; color:#fff">
                    <div class="form-group"></div>
                    <div class="navbar-header"></div>

                    <div class="container" style="margin-top: 20px;">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>