<?php
/**************************************
 * File Name: fullname-api.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-27
 * Project: CWEB280
 * Purpose: This filee looks for thefullname post parameter and returns a random welcome message
 *
 **************************************/

//set a variable to the fullname post param use an empty string a default
$fullnameparam = isset($_POST['fullname']) ? $_POST['fullname'] : "";

//validate the full name - must be at least 3 letters and at least 1 space - preg_match or explode
//$isValidFullName = preg_match('/.{3,}/', $fullnameparam);
if(!strlen($fullnameparam) >= 3 || empty(explode(' ', $fullnameparam, -1)))
{
    header('http/1.1 406 Not Acceptable');
    die(1);
}

//make an array of various welcome messages that the full name will be injected into - HINT: use sprintf format string
$welcomeMessages = ["Hello %s", "Hi %s", "Hello there %s", "You've arrived %s", "Grüß dich %s", "Moin moin %s"];

//randomly use one of the welcome messages and inject the full name - HINT: use sprintf function
$rand = rand(0, count($welcomeMessages) - 1);


$welcome = sprintf($welcomeMessages[$rand],$fullnameparam);
//$welcome = "Test";

header('Content-type:application/json');
//Return the welcome message as a JSON string
echo json_encode($welcome);