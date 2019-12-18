<?php
/**************************************
 * File Name: vehicle-api.php
 * User: ins226
 * Date: 2019-10-23
 * Project: CWEB280
 * this file will handle get, post, put and delete from the vehicle-ui
 * and return the appropriate JSON
 **************************************/
sleep(3);//mimic internet lag
require_once '../../lib/ORM/Repository.php';
require_once '../../lib/Vehicle.php';

//$_REQUEST contains the keys/values of both $_POST and $_GET - if it is empty then use php://input
$requestData = empty($_REQUEST)?json_decode(file_get_contents('php://input'),true) : $_REQUEST;//decode json as assoc array

$repo = new \ORM\Repository('vehicles.db');

//use switch to handle the various request methods
switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $resultToJSONEncode= handleGET($repo,$requestData['searchfor']);
        break;

    case 'POST':
        $vehicle = (new vehicle())->parseArray($requestData);
        $resultToJSONEncode= handlePOST($vehicle,$repo);
        break;

    case 'PUT':
        $vehicle = (new vehicle())->parseArray($requestData);
        $resultToJSONEncode= handlePUT($vehicle,$repo);
        break;

    default:
        header("http/1.1 405 Method Not Allowed");
        $resultToJSONEncode = "METHOD NOT ALLOWED";
}

$repo->close();


header('Content-type:application/json');
echo json_encode($resultToJSONEncode);

//GET - SELECT vehicleS
function handleGET($repo,$searchString=''){
    $vehicle = new vehicle();
    if(!empty($searchString)){
        $searchString='%'.$searchString.'%';
        $vehicle->givenName     = $searchString;
        $vehicle->preferredName = $searchString;
        $vehicle->familyName    = $searchString;
        $vehicle->userName      = $searchString;
        $useOR = true;
    }

    $result = $repo->select($vehicle,isset($useOR));
    if(!is_array($result)){
        header("http/1.1 409 Conflict");
        $result = $repo->getLastStatement();
    }
    return $result;
}

//POST - INSERT vehicle
function handlePOST($vehicle,$repo){
    $result=$vehicle->validate();
    if(count($result)){
        header("http/1.1 422 Unprocessable Entity");
    }elseif ($repo->insert($vehicle)>0){
        header("http/1.1 201 Created");
        $result = $vehicle; //return vehicle with new id and username
    }else{
        header("http/1.1 409 Conflict");
        $result = $repo->getLastStatement();
    }
    return $result;
}

//PUT - UPDATE vehicle
function handlePUT($vehicle,$repo){
    $result=$vehicle->validate();
    if(count($result)){
        header("http/1.1 422 Unprocessable Entity");
    }elseif ($repo->update($vehicle)>0){
        $result = $vehicle; //return vehicle as indicator of success
    }else{
        header("http/1.1 409 Conflict");
        $result = $repo->getLastStatement();
    }
    return $result;
}