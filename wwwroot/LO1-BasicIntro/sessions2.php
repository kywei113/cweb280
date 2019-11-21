<?php
/**************************************
 * File Name: sessions2.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-9/18/2019
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/
/*
 * Whenever your multi-page application uses sessions - ALWAYS add session_start() at the top of the page
 * EVEN IF the page code does not use session variables
 */
session_start();

foreach ($_SESSION as $sessionKey => $sessionVal) {
    $cleanSessionVal = htmlentities($sessionVal);

    $sessionHTML .= <<<EOT
        <li>$sessionKey: $cleanSessionVal</li>
EOT;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reading From Session Variables on Another Page</title>
</head>

<body>
<h1>Reading From Session Variables on Another Page</h1>

<div>
    <h2>Output values from the session - SAFELY</h2>
    <ol>
        <?php echo $sessionHTML ?>

<!--        <li>userid: --><?//= htmlentities($_SESSION['userid'])?><!--</li>*/-->
<!--        <li>userfirstname: --><?//= htmlentities($_SESSION['userfirstname'])?><!--</li>-->
<!--        <li>userlastname: --><?//= htmlentities($_SESSION['userlastname'])?><!--</li>-->
    </ol>
</div>

</body>
</html>