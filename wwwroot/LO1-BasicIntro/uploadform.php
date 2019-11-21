<?php
/**************************************
 * File Name: uploadform.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-9/13/2019
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/


$isPosted = $_SERVER['REQUEST_METHOD'] == 'POST';
$isValidFile1 = false;  //Good practice to set a default value for variables that have a chance of not being set during a POST
$isValidFile2 = false;
$isValidFile3 = false;

$validFileArray = [];   //Starting with an empty array - add items as we go through the loop

if($isPosted)
{
/*    //Must be a word doc up to 20KB inclusive in size
    $isValidFile1 = isset($_FILES['file1']) && $_FILES['file1']['size'] > 0 && $_FILES['file1']['size'] <= 1024 * 20 && $_FILES['file1']['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    if($isValidFile1)
    {
        $fileName = $_FILES['file1']['name'];
        //MINICISE #5 - move the file from the temp folder to the uploads folder
        //Hint: PHP relative files look like '../../'
        move_uploaded_file($_FILES['file1']['tmp_name'], "../../uploads/" . uniqid() . $fileName);
    }

    //Handle file upload and validation - allow image files up to 1.5MB
    $testImage = strpos($_FILES['file2']['type'], "image");

    $isValidFile2 = isset($_FILES['file2']) && $_FILES['file2']['size'] > 0 && $_FILES['file2']['size'] <= (1024 * 1536) && $testImage >= 0;        //=== is the EXACT match, data type and value
    if($isValidFile2)
    {
        $fileName = $_FILES['file2']['name'];
        move_uploaded_file($_FILES['file2']['tmp_name'], "../../uploads/" . uniqid() . $fileName);
    }


    //Validate files and allow ones up to 2MB in size
    //IMPORTANT: PHP has a maximum upload size of 2MB
    $isValidFile3 = isset($_FILES['file3']) && $_FILES['file3']['size'] >= (1024 * 1024 * 2);
    if($isValidFile3)
    {
        $fileName = $_FILES['file3']['name'];
        move_uploaded_file($_FILES[file3]['tmp_name'], "../../uploads/" . uniqid() . $fileName);
    }*/


    //The more mature solution for the above code

    foreach($_FILES as $fileKey=>$fileInfoArray)
    {
        //We use the $fileKey as a flag/semaphore. It acts as the primary check before checking other conditions
        $isValidFile = $fileInfoArray['size'] > 0 && $fileInfoArray['size'] <= (1024 * 1024 * 2) &&
            (($fileKey == 'file1' && $fileInfoArray['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        ||  ($fileKey == 'file2' &&  strpos($fileInfoArray['type'], 'image/') === 0)
        ||  ($fileKey == 'file3'));

        $validFileArray[$fileKey] = $isValidFile;

        if($isValidFile)
        {
            move_uploaded_file($fileInfoArray['tmp_name'], '../../uploads' . uniqid() . $fileInfoArray['name']);
        }
    }
}

$fileKeys = ['file1','file2','file3'];
$fileLabelNum = 0;

foreach($fileKeys as $fileKey)
{
    $fileLabelNum++;
    $errMsg = $isPosted && !$validFileArray[$fileKey] ? '<span class="error">You must upload a valid file</span>' : "";

    $fileInputHTML .= <<<EOT
    <div>
        <label>Upload File $fileLabelNum:
            <input type="file" name="$fileKey"/>
        </label>
        $errMsg
        
    </div>
EOT;

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload Files Form Example</title>

    <style>
        span.error
        {
            color: red;
            display: block;
        }
    </style>

</head>

<body>
<h1>Upload Files Form Example</h1>

<div>
    <h2>Upload Files</h2>

    <!-- IMPORTANT: form method MUST BE POST and enctype MUST BE MULTIPART/FORM-DATA -->
    <form method="post" action="#" enctype="multipart/form-data">
        <!-- <div>
            <label>Upload file 1:
                <input type="file" name="file1"/>
            </label>
            <Minicise 6 - Use validFileArray to create error messages
            <?php if($isPosted && !$validFileArray['file1']) { ?>
                <span class="error">You must choose a word doc to upload</span>
            <?php } ?>
        </div>

        <div>
            <label>Upload file 2:
                <input type="file" name="file2"/>
            </label>

            <?php if($isPosted && !$validFileArray['file2']) { ?>
                <span class="error">You must choose an image to upload</span>
            <?php } ?>
        </div>


        <div>
            <label>Upload file 3:
                <input type="file" name="file3"/>
            </label>

            <?php if($isPosted && !$validFileArray['file3']) { ?>
                <span class="error">You must upload a file up to 2MB</span>
            <?php } ?>
        </div> -->
        <?= $fileInputHTML ?>
        <input type="submit" />
    </form>
</div>

<div>
    <h2>DEBUG vardump $_FILES</h2>
    <?php var_dump($_FILES)?>
    <?php var_dump($validFileArray)?>

</div>
</body>
</html>