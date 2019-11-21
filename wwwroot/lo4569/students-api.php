<?php

use ORM\Repository;

/**************************************
 * File Name: students-api.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-23
 * Project: CWEB280
 *  This file will handle GET, POST, PUT, and DELETE from the Student-UI
 * and return the appropriate JSON
 *
 **************************************/
//MINICISE 22: GET all students in the DB and output JSON
require_once '../../lib/Student.php';
require_once '../../lib/ORM/Repository.php';

//If FormData js object is not used in the UI, then PHP will not fill $_POST superglobal
//We need to use a file that is created at 'php://input'
//$_REQUEST contains the keys/values of both $_POST and $_GET - if $_REQUEST is empty, use php://input
$requestData = empty($_REQUEST) ? json_decode(file_get_contents("php://input"), true) : $_REQUEST;
//$requestData = json_decode(file_get_contents("php://input"), true); //Decode JSON as assoc array

$repo = new ORM\Repository('../../db/students-alt.db');

//Uses a switch to check the request type and call the respective method to handle the request
switch ($_SERVER['REQUEST_METHOD'])
{
    case 'GET':
        $resultToJSONEncode = handleGET($repo,$requestData['searchfor']);
        break;
    case 'POST':
        $student = (new Student())->parseArray($requestData);
        $resultToJSONEncode = handlePOST($student, $repo);
        break;
    case 'PUT':
        $student = (new Student())->parseArray($requestData);
        $resultToJSONEncode = handlePUT($student, $repo);
        break;
    case 'DELETE':
        $student = new Student();
        $student->studentID = $requestData['id'];   //URL params get put in the $_GET. Used a ternary earlier to use either $_REQUEST or php://input
        $resultToJSONEncode = handleDELETE($student, $repo);
        break;
    default:
        header("http/1.1 405 Method Not Allowed");
        $resultToJSONEncode = "METHOD NOT ALLOWED";
}

$repo->close();

header('Content-type:application/json');
echo json_encode($resultToJSONEncode);


//GET - SELECT all students
function handleGET($repo, $searchString)
{
    $student = new Student();
    if(!empty($searchString))
    {
        //Set values for all text student properties using wildcards
        $student->familyName = '%' . $searchString . '%';
        $student->givenName = '%' . $searchString . '%';
        $student->preferredName = '%' . $searchString . '%';
        $student->userName = '%' . $searchString . '%';
    }
    $result = $repo->select($student, true);

    if(!is_array($result))  //If it's not an array, error code was returned
    {
        header("http/1.1 418 I'm a teapot");
        $result = $repo->getLastStatement();
    }
    else
    {
        if(empty($result))
        {
            //404 if student isn't found
            header("http/1.1 404 Not Found");

        }
    }

    ///Default status code is 200, don't need to set the header
    return $result;




}

//POST - INSERT Student
function handlePOST($student, $repo)
{
    $student->studentID=rand(1001,9999);       //Use rand number for now. Will use auto increments later //Not needed if autoincrementing
    $student->userName=strtolower($student->familyName) . rand(1000,9999);

    //Minicise 31: Call Validate and return the appropriate information

    $result = $student->validate();
    if(count($result) >0 )
    {
        header("http/1.1 422 Unprocessable Entry");
    }
    else
    {
        if($repo->insert($student)<1)   //Indicated Database Error - If insert returns and int less than 1
        {
            header("http/1.1 418 I'm a teapot");
            $result = $repo->getLastStatement();
        }
        else
        {
            header("http/1.1 201 Created");
            $result = $student;    //Sends back generated ID and username, success status code 201
        }
    }
    return $result;
}

//PUT - UPDATE Student
function handlePUT($student, $repo)
{
    $result = $student->validate();
    if(count($result) >0 )
    {
        header("http/1.1 422 Unprocessable Entry");
    }
    else
    {
        if($repo->update($student)<1)   //Indicated Database Error - If insert returns and int less than 1
        {
            header("http/1.1 418 I'm a teapot");
            $result = $repo->getLastStatement();
        }
        else
        {
//            header("http/1.1 200 "); 200 OK is the default header for the return, don't need to explicitly set
            $result = $student;    //Sends back the updated student and success status code 201
        }
    }
    return $result;
}

//DELETE - DELETE Student
function handleDELETE($student, $repo)
{
    $result = $repo->delete($student);

    if ($result < 1)
    {
        header("http/1.1 418 I'm a teapot");
        $result = $repo->getLastStatement();
    }
    else
    {
        header("http/1.1 204 No Content");
        $result = null; //Indicates student was deleted, so no student is returned
    }

    return $result;
}