<?php
/**************************************
 * File Name: testtest.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-13
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/

$fileUploads = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
        materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>

    <title>Title</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1>Title</h1>
    <p></p>
</div>

<div class="container">


    <form method="post" action="" enctype="multipart/form-data">
        <h1><strong><em><u>File-o-Tron 5000</u></em></strong></h1>
        <input type="file" name="fileUploads[]" multiple />

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- span element to echo out the generated file list -->
    <span><?= $fileListHeader ?></span>


<!--     VAR DUMPS FOR DEBUGGING -->


        <footer class="row bg-info">
            <div class="col-sm-4">
                <h3>Vardump $_FILES</h3>
                <pre><?php var_dump($_FILES) ?></pre>
            </div>

            <div class="col-sm-4">
                <h3>Vardump $_SESSION</h3>
                <pre><?php var_dump($_SESSION) ?></pre>
            </div>

            <div class="col-sm-4">
                <h3>Vardump $column</h3>
                <pre><?php  var_dump($fileUploads) ?></pre>
            </div>
        </footer>
</div>



</body>
</html>