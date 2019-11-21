<?php
/**************************************
 * File Name: index.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-20
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/
session_start();


//MINICISE 10: Add code so that if the user tries to access index.php without logging in, redirect them to the login-logout page
//HINT: See if a certain session variable exists.
if(!isset($_SESSION['useremail']) || !isset($_SESSION['userpassword']))
{
    header('Location: cst234-login-logout.php');
    die(1);
}

$isPosted = ($_SERVER["REQUEST_METHOD"] == "POST");

if($isPosted)
{
    $_SESSION['signout'] = $_POST['signout'];

    //Redirect to given page
    header('Location: cst234-login-logout.php');

    //Kills the rest of the page execution
    die(1);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
        materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>

    <title>Welcome</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1>Welcome: <?= htmlentities($_SESSION['useremail']) ?></h1>
    <p></p>
</div>
<div class="container">
    <form method="post" action="">
        <button type="submit" class="btn btn-primary" name="signout" value="Sign Out">Sign Out</button>
    </form>

    <footer class="row bg-info">
        <div class="col-sm-4">
            <h3>Vardump $_POST</h3>
            <p><?php var_dump($_POST) ?></p>
        </div>
        <div class="col-sm-4">
            <h3>Vardump $_COOKIE</h3>
            <p><?php var_dump($_COOKIE) ?></p>
        </div>
        <div class="col-sm-4">
            <h3>Vardump $_SESSION</h3>
            <p><?php var_dump($_SESSION) ?></p>
        </div>
    </footer>
</div>



</body>
</html>