<?php
/**************************************
 * File Name: regions-api.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-25
 * Project: CWEB280
 * NOTE: this file has no closing php tag " ?> " because it is NOT meant to return HTML
 * This file will return JSON string - NOT the default HTML string
 * IMPORTANT: DO NOT USE VAR_DUMP. Var_Dump returns HTML, not a suitable JSON string. It will break JSON
 **************************************/

$country = isset($_GET['country']) ? $_GET['country'] : "";

switch($country)
{
    case "ca":
        //in JSON, [] is for ARRAYS, { } is for OBJECTS
        $regions = ['AB'=>'Alberta', 'MB'=>'Manitoba','SK'=>'Saskatchewan'];
        break;

    //Add the case for US states
    case "us":
        $regions = ['CA'=>'California', 'IL'=>'Illinois', 'OR'=>'Oregon'];
        break;

    default:
        $regions = ["code"=>"1", "message"=>"Invalid Country"];
        header("http/1.1 406 Not Acceptable");
        break;
}




//Convert the associative array into a 2D array to eventually produce a JSON array of objects
//Example: Need to change {"AB:"ALBERTA",...} to [{"abbr":"AB", "name":"Alberta"},...]

//Use a for loop for future functionality
$models = [];   //Empty array
foreach($regions as $regionKey=>$regionVal)
{
    $models[] = ['abbr'=>$regionKey, 'name'=>$regionVal];       //Creates a new array entry per entry in $regions
}

$regions = $models;     //Overwrites $regions array with $models array



//Tell browser to expect JSON, not HTML
header("Content-type:application/json");

//Output and return JSON - json_encode serializes PHP objects/arrays into JSON string
echo json_encode($regions);

/*
 * JSON does  not support Associative arrays, so json_encode treats associative arrays as JSON objects
 * where the ARRAY KEY is the PROPERTY NAME, and the ARRAY VALUE is the PROPERTY VALUE
 */


