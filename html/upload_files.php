<!DOCTYPE html>
<html>
<head>
    <title>Upload para S3</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .upload-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .preview-img {
            max-width: 100%;
            margin-top: 15px;
            display: none;
        }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>
    
    <div class="container">
        <div class="upload-container">
            <h2 class="text-center">Upload de Arquivos para S3</h2>
            
            <form action="upload_handler.php" method="post" enctype="multipart/form-data" id="uploadForm">
                <div class="form-group">
                    <label for="fileToUpload">Selecione o arquivo:</label>
                    <input type="file" class="form-control-file" id="fileToUpload" name="fileToUpload" required>
                </div>
                
                <div class="form-group">
                    <img id="preview" class="preview-img" alt="Pré-visualização">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Enviar para S3</button>
            </form>
            
            <div id="statusMessage" class="mt-3"></div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script>
    // Pré-visualização da imagem antes do upload
    document.getElementById('fileToUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('preview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Envio via AJAX
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        $('#statusMessage').html('<div class="alert alert-info">Enviando arquivo...</div>');
        
        $.ajax({
            url: 'upload_handler.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#statusMessage').html('<div class="alert alert-success">'+response+'</div>');
                $('#preview').hide();
                $('#uploadForm')[0].reset();
            },
            error: function(xhr) {
                $('#statusMessage').html('<div class="alert alert-danger">Erro: '+xhr.responseText+'</div>');
            }
        });
    });
    </script>
</body>
</html>