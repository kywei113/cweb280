<?php
/**************************************
 * File Name: filelocker-api.php
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

header('Content-type:application/json');
echo json_encode($_SESSION['files']);