<?php
/**************************************
 * File Name: filelocker-api.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-01
 * Project: CWEB280
 * CWEB280 A1 Q2 - FileLocker API
 *
 * Handles parsing of $_FILES into $_SESSION variables (fileArray, allFileSize).
 * Adds a unique name (UID) property to all objects and uploads files to current directory using the UID
 * Echoes back the $_SESSION superglobal as a JSON object
 *
 **************************************/

session_start();

header('Content-type:application/json');

//Checking if ['fileArray'] in $_SESSION exists yet, creates it if it doesn't
if(!isset($_SESSION['fileArray']))
{
    $_SESSION['fileArray'] = [];
}

//Setting allFileSize to 0 every call. Recalculated later
$_SESSION['allFileSize'] = 0;

//Creating an empty array for error messages
$_SESSION['fileErrors'] = [];

//Checks if anything is set in $_FILES. On initial page population, $_FILES won't have anything set
//Prevents $_SESSION assignment code from running when unnecessary (no files to add)
if(isset($_FILES))
{
    //Iterating through incoming $_FILES and pushing them into session fileArray.
    //Will append to existing session files if any already exist (fileArray is not recreated every time API is accessed)
    $newFiles = [];
    foreach($_FILES as $fileKey => $fileVal)
    {
        //Checking size of each incoming file. If 0 or less, or greater than 2MB add an error to the error array
        if($fileVal['size'] <= 0 || $fileVal['size'] > (1024 * 1024 * 2))
        {
            array_push($_SESSION['fileErrors'], "INVALID FILE: " . $fileVal['name'] . " has an invalid file size. Must be between 0KB and 2MB");
        }
        else    //Push array of properties into fileArray if it's valid
        {
            array_push($newFiles, array('name' => $fileVal['name'],'tmp_name' =>$fileVal['tmp_name'],'size'=>$fileVal['size']));
        }
    }

    /**
     * Iterating through $_SESSION['fileArray'] and performing the following:
     *  1)Dividing the file name around the "." in the extension
     *      Used substr to create strings from start to position of last "." (separates filename and extension)
     *      strrpos used to avoid issues with files with multiple "."'s in the name by finding the last instance of "."
     *          strrpos - https://www.php.net/manual/en/function.strrpos.php
     *          substr - https://www.php.net/manual/en/function.substr.php
     *      Pushed two portions of the name into the $splitName array
     *  2)Concatenated each side of SplitName with "_uniqid()" between them. Results in "filename_uniqID.extension"
     *      Assigned name to the UID of the file within the session fileArray
     *  3)Added current file's size to session's allFileSize
     *  4)Upload current file to current folder using UID name
     */
    foreach($newFiles as $newFileKey => $newFileVal)
    {
        //Splits original name into extension and filename. Adds underscore and random value after the name and re-adds the extension

        //I got tired of typing $sessionVal['name']
        //Sanitizing names for html just in case
        $currentName = htmlentities($newFileVal['name']);
        $splitName = [];

        array_push($splitName, substr($currentName, 0, strrpos($currentName, '.')));
        array_push($splitName, substr($currentName, strrpos($currentName, '.')));

        //Sanitizing names for html just in case
        array_push($newFileVal['UID'] = htmlentities($splitName[0] . "_" . uniqid() . $splitName[1]));

        //Adding all file sizes together for running total
        $_SESSION['allFileSize'] += $newFileVal['size'];

        //Moves the temporary file to current directory, renames it to the UID name
        move_uploaded_file($newFileVal['tmp_name'], $newFileVal['UID']);

        array_push($_SESSION['fileArray'], $newFileVal);
    }
}

/**
 * Echoing $_SESSION as JSON string
 */
echo json_encode($_SESSION);
