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

                    <form name="input" style="width: 90%;" action="s3-write-config.php" method="post" class="form-horizontal">
                        <div class="form-group" style="margin-top: 20px;">
                            <div class="col-sm-10"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input class="btn btn-primary btn-sm" type="submit" class="btn btn-default"/>
                            </div>
                        </div>
                    </form>

                    <!-- Incluindo a lista de arquivos do S3 -->
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