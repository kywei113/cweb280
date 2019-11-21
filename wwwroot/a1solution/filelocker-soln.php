<?php
/**************************************
 * File Name: filelocker.php
 * User: ins226
 * Date: 2019-10-01
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/
session_start();

//loop through each of the uploaded files to save info to session and move files to current folder
foreach($_FILES as $fileInfo){ //never use the $key so just loop through values in $_FILES which is the fileInfo array
    if($fileInfo['size']>0) { //ensure file is not empty
        $uniqueName = uniqid() . $fileInfo['name']; //generate a unique file name to save on the server
        //store the required data from the fileInfo , size in KB and unique name to session variable
        $_SESSION['files'][] = ['name' => $fileInfo['name'], 'size' => $fileInfo['size']/1024, 'uniqueName' => $uniqueName];
        //move the uploaded file from the temp directory to the current folder with a unique name
        move_uploaded_file($fileInfo['tmp_name'],$uniqueName);
    }
}

/*Bonus sort session files array by original name
 * https://www.php.net/manual/en/function.array-multisort.php - example 3 and example 4 (case insensitive) */
$names = array_column($_SESSION['files'],'name'); //pull out just the name values from the 2D array into a 1D array
$namesLower = array_map('strtolower',$names ); //convert all name values in the array  to lowercase - effectively causes case insensitive sort
//call multisort and use the lowercase names array as the items to sort ascending and compare as strings
//use the sorted lowercase names index numbers to set the order of the 2D $_SESSION['files'] array
array_multisort($namesLower, SORT_ASC, SORT_STRING,  $_SESSION['files']);

//loop through session file list to generate HTML
$totalFileMB =0;
$fileListHTML='';
foreach($_SESSION['files'] as $sessInfo) {
    $uniqueName = htmlentities($sessInfo['uniqueName']); //set variable to use in HEREDOC notation
    $name = htmlentities($sessInfo['name']);//set variable to use in HEREDOC notation
    //extra round size to 2 decimals https://www.php.net/manual/en/function.round.php
    $size = round($sessInfo['size'], 2);//set variable to use in HEREDOC notation
    $totalFileMB += $sessInfo['size']/1024; //running total of all sizes, divide by 1024 to convert to MB

    //generate table row HTML with HEREDOC notation and concatenate html to $fileListHTML
    $fileListHTML .= <<<EOT

        <tr>
            <td><a href="$uniqueName" target="_blank">$name </a></td>
            <td>$size</td>
        </tr>

EOT;
}


$fileInputsHTML = '';
//loop to generate html for 5 form inputs of type file
for($i=1 ; $i<=5; $i++){

    //concatenate file input html to $fileInputsHTML
    $fileInputsHTML .= <<<EOT

        <div class="form-group">
            <label for="inputFile$i">Select File$i:</label>
            <input type="file" name="file$i" class="form-control" id="inputFile$i">
        </div>    
EOT;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
        materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <title>File Locker PHP only</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1>File Locker PHP only</h1>
    <p></p>
</div>
<div class="container">
    <!--FILE INPUTS and SUBMIT-->
    <form method="post" action="" enctype="multipart/form-data">
        <?= $fileInputsHTML ?><!--output generated file inputs HTML-->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!--FILE LIST bootstrap table classes https://www.w3schools.com/bootstrap4/bootstrap_tables.asp -->
    <table class="table table-hover table-striped">
        <tr class="table-info">
            <!--output total file size in MB rounded to 2 decimal places -->
            <th colspan="2">Total File Size: <?= round($totalFileMB,2) ?>MB</th>
        </tr>
        <tr class="table-primary">
            <th>File Name</th>
            <th>File Size (KB)</th>
        </tr>
        <?= $fileListHTML ?><!--output generated file list table row HTML-->
    </table>

    <footer class="row bg-info mt-5">
        <div class="col-sm-4">
            <h3>Vardump $_FILES</h3>
            <p><?php var_dump($_FILES) ?></p>
        </div>
        <div class="col-sm-4">
            <h3>Vardump $_SESSION</h3>
            <p><?php var_dump($_SESSION) ?></p>
        </div>
        <div class="col-sm-4">
            <h3>Vardump $_POST</h3>
            <p><?php var_dump($_POST) ?></p>
        </div>
    </footer>
</div>

</body>
</html>