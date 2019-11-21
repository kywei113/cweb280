<?php
/**************************************
 * File Name: sessions1.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-9/18/2019
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/

/*
 * The $_SESSION super global is the only super global devleopers can directly set values into
 * Example: $_SESSION['key']='Value' OR $_SESSION['key'] = ['value1','value2,'value3']
 *
 * Session data (a.k.a. session variables) is stored on the server not the browser
 * so it is safe for storing sensitive or private information (unlikes cookies)
 *
 * Each browser/user that makes requests to the server can have their own protected session
 * storage.
 *
 * Question: How does the server know which user/browser owns which session information?
 * Answer: Session ID stored as a cookie, form field, or URL
 */


//Start the session storage and set a cookie on the browser
session_start();

//Set session variables for userid, userFirstName, userLastName - IF they are not already set
$_SESSION['userid'] = isset($_SESSION['userid']) ? $_SESSION['userid'] : "005";
$_SESSION['userfirstname'] = isset($_SESSION['userfirstname']) ? $_SESSION['userfirstname'] : "Timmy";
$_SESSION['userlastname'] = isset($_SESSION['userlastname']) ? $_SESSION['userlastname'] : "Jimmy";

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Session Examples in PHP</title>
</head>

<body>
<h1>Session Examples in PHP</h1>

<div>
    <h2></h2>
    <a href="sessions2.php">Goto Page that reads/outputs session variables</a>

</div>

<div>
    <h2>var dump $_SESSION</h2>
    <?php var_dump($_SESSION); ?>
</div>

</body>
</html>