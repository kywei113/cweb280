<?php
/**************************************
 * File Name: fileupload-api.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-02
 * Project: CWEB280
 * Purpose: This file will receive files/posted files and return a JSON serialized version of the $_FILES superglobal
 *
 **************************************/



header('Content-type:application/json');


//MINICISE 15 Move all the uploaded files into the uploads folder with a unique name
//HINT: We did this before
foreach($_FILES as $fileKey => $fileVal)
{
    move_uploaded_file($fileVal['tmp_name'], '../../uploads/' . uniqid() . $fileVal['name']);
}

echo json_encode($_FILES);





