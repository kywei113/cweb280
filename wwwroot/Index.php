<?php
/**************************************
 * File Name: Index.php
 * User: cst234
 * Date: 2019-09-9/4/2019
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/

//Declaring the first variable - Variables start with a $
$msg = "Hi this is from PHP"; //Stores text within the $msg variable

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CWEB280</title>
<!--    <script>alert("Hello World!")</script>-->
<!--    <script>alert("<?php //echo $msg ?>//")</script>   You can inject PHP into JavaScript. PHP is just read as text by the server -->
    <style>
        li
        {
            font-size: 15pt;
        }
        div:nth-child(odd)
        {
            background-color: lightgray;
        }

        #phpHead
        {
            background-color: white;
        }

    </style>
</head>

<body>
    <h1>Welcome to CWEB280</h1>

    <div id="phpHead">
        <h2>
            <?php echo $msg ?>
        <h2>
    </div>

    <div>
        <h1>Learning Outcome 1</h1>
        <ul>
            <li><a href="LO1-BasicIntro/structures.php">LO1 - Structures</a></li>
            <li><a href="LO1-BasicIntro/forms.php">LO1 - Forms</a></li>
            <li><a href="LO1-BasicIntro/uploadform.php">LO1 - Upload Form</a></li>
            <li><a href="LO1-BasicIntro/cookies.php">LO1 - Cookies</a></li>
            <li><a href="LO1-BasicIntro/sessions1.php">LO1 - Sessions 1</a></li>
            <li><a href="LO1-BasicIntro/sessions2.php">LO1 - Sessions 2</a></li>
            <li><a href="loginexercise/cst234-login-logout.php">Login-Logout Exercise</a></li>
        </ul>
    </div>

    <div>
        <h1>Learning Outcome 8</h1>
        <ul>
            <li><a href="lo8-ajax/regions-api.php">LO8-AJAX Regions API</a></li>
            <li><a href="lo8-ajax/regions-ui.php">LO8-AJAX Regions UI</a></li>
            <li><a href="lo8-ajax/fileupload-api.php">LO8-FileUpload API</a></li>
        </ul>
    </div>

    <div>
        <h1>Learning Outcome 4, 5, 6, 9</h1>
        <ul>
            <li><a href="lo4569/usedbclasses.php">Use DB Classes</a></li>
            <li><a href="lo4569/student-ui.php">Student UI</a></li>
            <li><a href="lo4569/students-api.php">Student API</a></li>
        </ul>
    </div>
    <div>
        <h1>Assignment 1</h1>
        <ul>
            <li><a href="cst234cweb280a1/filelocker.php">FileLocker - PHP Only</a></li>
            <li><a href="cst234cweb280a1/filelocker-ui.php">FileLocker - Vue/Axios - UI</a></li>
            <li><a href="cst234cweb280a1/filelocker-api.php">FileLocker - Vue/Axis - API</a></li>

        </ul>
    </div>

    <div>
        <h1>Midterm 1 Practice</h1>
        <ul>
            <li><a href="mt1practice/practiceAPI-ui.php">API Practice - UI</a></li>
            <li><a href="mt1practice/practiceAPI-api.php">API Practice - API</a></li>
        </ul>
    </div>

    <div>
        <h1>Assignment 2</h1>
        <ul>

        </ul>
    </div>

    <div>
        <h1>PHP Info</h1>
        <ul>
            <li><a href="phpInfo.php">PHP Info</a></li>
        </ul>
    </div>


</body>
</html>