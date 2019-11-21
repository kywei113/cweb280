<?php
/**************************************
 * File Name: filelocker.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-01
 * Project: CWEB280
 * CWEB280 A1 Q1 - File Locker PHP Solution
 *
 * Page for allowing users to upload files, store information in $_SESSION and display
 * information for uploaded session files. Uploads files to current directory and
 * generates links and other information based on uploaded files.
 **************************************/

/*****SERVER BLOCK*******/
session_start();

//Checking if request is a POST
$isPosted = $_SERVER['REQUEST_METHOD'] == 'POST';

$fileListHeader = "";

if($isPosted)
{
    if(!isset($_SESSION['fileArray']))
    {
        $_SESSION['fileArray'] = [];
    }

    //Checks initial number of file entries stored in $_SESSION['fileArray'], sets initCount to 0 if no entries
    $initCount = isset($_SESSION['fileArray']) ? sizeof($_SESSION['fileArray']) : 0;
    $newFiles = [];
    //Loops through uploaded files
    foreach($_FILES['fileUploads'] as $fileKey => $fileVal)
    {
        //Resets currentCount to initial count
        $currentCount = $initCount;

        //Loops through each entry within each file property (multiple makes weird to iterate through)
        foreach($fileVal as $val)
        {
            //Adds a new file entry to the end of the $_SESSION file array and assigns keys and values
            //Increments current count as it goes
            if($fileKey == 'name' || $fileKey == 'size' || $fileKey == 'tmp_name')
            {
                $newFiles[$currentCount++][$fileKey] = $val;
            }
        }
    }

//    Loops through entries within the fileArray stored in $_SESSION
    foreach($newFiles as $newFileKey => $newFileVal)
    {
        /**
         * 1)Dividing the file name around the "." in the extension
         *      Used substr to create strings from start to position of last "." (separates filename and extension)
         *      strrpos used to avoid issues with files with multiple "."'s in the name by finding the last instance of "."
         *          strrpos - https://www.php.net/manual/en/function.strrpos.php
         *          substr - https://www.php.net/manual/en/function.substr.php
         *      Pushed two portions of the name into the $splitName array
         *  2)Concatenated each side of SplitName with "_uniqid()" between them. Results in "filename_uniqID.extension"
         *      Assigned name to the UID of the file within the session fileArray
         */
        $currentName = htmlentities($newFileVal['name']);
        $splitName = [];

        array_push($splitName, substr($currentName, 0, strrpos($currentName, '.')));
        array_push($splitName, substr($currentName, strrpos($currentName, '.')));

        //Sanitizing names for html just in case
        $newFileVal['UID'] = htmlentities($splitName[0] . "_" . uniqid() . $splitName[1]);

        //Adding all file sizes together for running total
        $_SESSION['allFileSize'] += $newFileVal['size'];

        //Moves the temporary file to current directory, renames it to the UID name
        move_uploaded_file($newFileVal['tmp_name'], $newFileVal['UID']);

        array_push($_SESSION['fileArray'], $newFileVal);
    }
}

//Checking if any entries already exist within $_SESSION
if(isset($_SESSION['fileArray']))
{
    //tracking total submitted file size for output later
    $fileTotalSize = 0;

    //String for the List Item HTML to be generated later
    $fileListHTML = "";

    /*Utilizing the array_column and array_multisort functions.
    Sourced from https://www.php.net/manual/en/function.array-multisort.php and https://www.php.net/manual/en/function.array-column.php

    array_column used to retrieve a specific "column" from the file array, isolating Name.
    Ascending sort by case-insensitive string applied to $column, and then overall 'fileArray' is sorted by the common 'name' key
    */
    $column = array_column($_SESSION['fileArray'], 'name');
    array_multisort($column, SORT_ASC, SORT_STRING|SORT_FLAG_CASE, $_SESSION['fileArray']);

    /*
     * Building the Header and List of files that were submitted
     */
    foreach($_SESSION['fileArray'] as $sessionKey => $sessionVal)
    {
        //Grabbing the unique name of a file
        $fileListItem = htmlentities($_SESSION['fileArray'][$sessionKey]['UID']);

        //Getting the original name of the file
        $fileListName = htmlentities($_SESSION['fileArray'][$sessionKey]['name']);

        //Getting the size of the individual file, converting to KB and rounding it to 4 decimal points
        $fileListSize = round($_SESSION['fileArray'][$sessionKey]['size'] / 1024 , 4);

        //Running total of all file sizes tracked by $_SESSION, converted into MB. Left decimals open
        $fileTotalSize += $_SESSION['fileArray'][$sessionKey]['size'] / 1024 / 1024;

        //Concatenating unordered list together with link elements within each list item.
        //Links to the unique name of the file while displaying the original name
        $fileListHTML .= <<<EOT
        <ul>
            <li><a href="$fileListItem">$fileListName</a> - $fileListSize KB</li>
        </ul>
EOT;
    }

    //Rounds the total file size to 8 decimal points
    $fileTotalSize = round($fileTotalSize, 8);

    //Creates the Submitted Files header with total size in MB
    $fileListHeader = <<<EOT
            <h2>Submitted Files - $fileTotalSize MB Total</h2>
EOT;

    //Merges the Header and List HTML together
    $fileListHeader .= $fileListHTML;
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>

    <title>File Locker - PHP Solution</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1><strong>File Locker - PHP Solution</strong></h1>
    <h3>Kyle Wei - CST234</h3>
    <p>Var Dumps are commented out in code</p>
</div>

<div class="container">


    <form method="post" action="" enctype="multipart/form-data">
        <h1><strong><em><u>File-o-Tron 5000</u></em></strong></h1>
        <input type="file" name="fileUploads[]" multiple />

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- span element to echo out the generated file list -->
    <span><?= $fileListHeader ?></span>

<!--                         -->
<!-- VAR DUMPS FOR DEBUGGING -->
<!--                         -->

<!--    <footer class="row bg-info">-->
<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump $_FILES</h3>-->
<!--            <pre>--><?php //var_dump($_FILES) ?><!--</pre>-->
<!--        </div>-->
<!---->
<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump $_SESSION</h3>-->
<!--            <pre>--><?php //var_dump($_SESSION) ?><!--</pre>-->
<!--        </div>-->
<!---->
<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump $column</h3>-->
<!--            <pre>--><?php // var_dump($column) ?><!--</pre>-->
<!--        </div>-->
<!--    </footer>-->
</div>



</body>
</html>