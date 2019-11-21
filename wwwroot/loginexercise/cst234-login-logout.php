<?php
/**************************************
 * File Name: cst234-login-logout.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-20
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/
/*
 * PRACTICAL EXERCISE
 * User logs in, if all fields are valid, redirect to the Welcome page
 * Welcome page displays Welcome, email, and has a signout button
 *
 */

/*
 * TASK 1 - Handle the Login Post
 *      If email and password are valid, save them to session and redirect to index.php (not yet created)
 *      If any input is invalid - show error messages and what the user type/checked
 *
 *
 * TASK 2 / MINICISE 9
 * You have all been to a login page that has a "Remember Me" checkbox.
 * If the user checks "Remember Me", save a posted and valid email to a cookie that lasts 10 days
 *
 * If the cookie is set, put the cookie email in the email textbox
 *
 * DO NOT BREAK the functionality that's already in there now
 */
session_start();

$passwordLength = 8;

$rememberCookieExists = false;

$isPosted = ($_SERVER["REQUEST_METHOD"] == "POST");

//Checking if email and pw cookies set. Used to determine if the fields need to be from cookies. Only fills fields if both cookies are set.
if(!$isPosted && isset($_COOKIE['useremail']) && (isset($_COOKIE['userpassword'])))
{
    $rememberCookieExists = true;
}

if(!$isPosted && $_SESSION['signout'] === "Sign Out")
{
    $_SESSION = array();
}

$fieldsValid['email'] = $isPosted ? (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) : "noEntry";
$fieldsValid['password'] = $isPosted ? (isset($_POST['password']) && strlen($_POST['password']) >= $passwordLength)  : "noEntry";

if($isPosted && $fieldsValid['email'] && $fieldsValid['password'] )
{
    $_SESSION['useremail'] = $_POST['email'];
    $_SESSION['userpassword'] = $_POST['password'];

    if($_POST['rememberme'])
    {
        setcookie('useremail',$_POST['email'], time() + (60 * 60 * 24 * 10));
        setcookie('userpassword',$_POST['password'], time() + (60 * 60 * 24 * 10));
    }

    //Redirect to given page
    header('Location: index.php');

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

    <title>LOGIN-LOGOUT</title>
</head>


<body>
<div class="jumbotron text-center">
    <h1>LOGIN-LOGOUT</h1>
    <p></p>
</div>


<div class="container">
    <form method="post" action="" novalidate> <!-- novalidate disables client-side validation. Need to disable this to test server-side validation -->
        <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" name="email" class="form-control" id="email" value="<?= $rememberCookieExists ? htmlentities($_COOKIE['useremail']) : htmlentities($_POST['email'])?>">

            <?php if($fieldsValid['email'] == false) { ?>
                <span class="text-danger">Please enter a valid email</span>
            <?php }?>

        </div>


        <div class="form-group">
            <label for="pwd">Password:</label>
            <input type="password" name="password" class="form-control" id="pwd" value="<?= $rememberCookieExists ? htmlentities($_COOKIE['userpassword']) : htmlentities($_POST['password'])?>">

            <?php if($fieldsValid['password'] == false) { ?>
                <span class="text-danger">Please enter a valid password</span>
            <?php }?>
        </div>


        <div class="form-group form-check">
            <label class="form-check-label">
                <input type="checkbox" name="rememberme" class="form-check-input" <?= $_POST['rememberme'] ? "checked" : "" ?>> Remember me
            </label>
        </div>


        <button type="submit" class="btn btn-primary">Submit</button>
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

        <div class="col-sm-4">
            <h3>Vardump $_fields</h3>
            <p><?php var_dump($fieldsValid) ?></p>
        </div>
    </footer>
</div>



</body>
</html>