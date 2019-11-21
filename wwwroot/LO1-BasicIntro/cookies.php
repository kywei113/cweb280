<?php
/**************************************
 * File Name: cookies.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-9/18/2019
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/

/*So far we've covered a few super globals that are populated by PHP from the request which is sent by the browser
The request super globals are:
$_POST
$_GET
$_REQUEST - not used officially in the class room yet
$_FILES
$COOKIE
*/

/*
 * $_COOKIE is filled by PHP when the browser sends cookies to the website
 * Cookies are stored on the browser and can be set by the website server
 * i.e. the website sends a set cookie header in the response
 * The browser will not usually have the cookie when it makes the first request to the server
 * When the server responds it asks the browser to store a cookie (set cookie header)
 * After the browser stores the cookie locally the browser will automatically send the cookie information
 * to the server when the browser detects the website domain in the URL.
 * Each website can store cookies on the browser and the browser only sends cookies to the servers
 * that have permission to see the cookie
 *
 */

//set a cookie using php that expires in 30 days
//MINICISE 7: Add code to only set the cookie if the testcookieparam cookie does not exist
if(!key_exists('testcookieparam', $_COOKIE))
{
    setcookie('testcookieparam', 'this is some text that illustrates the value of a cookie', time() + (60*60*24*30));
}

//MINICISE 8: add code so that when the user submits the signout form, a cookie is set with an expiry date in the past
if($_POST['signout'] == 'Sign Out')
{
    setcookie('testcookieparam','the signout cookie', 1);
}

/*
 * CAUTION: NEVER EVER EVER store private or sensitive data in a cookie
 * Malicious website can use exploits to read cookie data from other websites
 */


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cookies Example IN PHP</title>
</head>

<body>
<h1>Cookies Example in PHP</h1>

<div>
    <h2>Sign Out</h2>
    <form method="post" action="#">
        <input type="submit" value="Sign Out" name="signout"/>
    </form>
</div>

<div>
    <h2>vardump $_COOKIE</h2>
    <?php var_dump($_COOKIE) ?>

    <h2>vardump $_POST</h2>
    <?php var_dump($_POST) ?>
</div>
</body>
</html>