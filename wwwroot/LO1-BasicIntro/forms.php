<?php
/**************************************
 * File Name: forms.php
 * User: Kyle Wei - cst234
 * Date: 2019-09-9/6/2019
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/

//PHP uses Super Global variables that stores data that was sent in the request
//Using Super Globals allows developers to make use of the user inputs to adjust the content of the page
//Tip of the Pros: Try not to use super globals directly. Store the value in your own variable.

//$postParam = $_POST["testpostparam"];   //Stores the user input for testpostparam in your own variable
//$urlParam = $_GET["testurlparam"];

$provinceArray = ['ab'=>'Alberta', 'mb'=>'Manitoba', 'sk'=>'Saskatchewan'];


//To improve user-friendliness, consider having default values for the user input data
//This is done so users don't get empty strings or labels that have no text after them
//Most of the time, the first time you hit the page there will be no POST or GET parameters
$postParam = isset($_POST["testpostparam"]) ? trim($_POST["testpostparam"]) : "Please enter a value";
$urlParam = isset($_GET["testurlparam"]) ? $_GET["testurlparam"] : "No URL parameters";
$agreeParam = isset($_POST["agreetoterms"]) ? $_POST["agreetoterms"] : "0";               //Always good to set a default value for inputs, especially checkboxes
$petTypeParam = isset($_POST["pettype"]) ? $_POST["pettype"] : "No Pet";
$province = isset($_POST["province"]) ? $_POST["province"] : "no province";

//Loop through the province array to generate an HTML string that contains options

foreach($provinceArray as $provAbrev=>$provName)
{
//    $provinceOptionsHTML .= "<option value=$provAbrev>$provName</option>";
    //Use HEREDOC notation to declare a string - best used when string contains HTML content
    //If the user submitted province is the same as the current abbreviation, output "selected" in to the option tag
    $provSelected = $province == $provAbrev ? "selected" : "";
    $provinceOptionsHTML .= <<<EOT
        
                            <option value="$provAbrev" $provSelected>$provName</option>
EOT;

}

//Consider altering your page content based on whether form was posted/submitted
//Determine if the page was posted/submitted

$isPosted = ($_SERVER["REQUEST_METHOD"] == "POST");

//Validating User Input - Ensure user enters data that your application will accept
$isValidPostParam = !empty($postParam); //Checks if postParam is an empty string or not. If it is not empty (returns false) then it is valid

$isValidAgreeToTerms = $agreeParam == "1"; //Only 1 is accepted by the checkbox. Checks if agreeParam has a value of 1

//MINICISE #4: Come up with a validation check (conditional statement) that only allows dog, cat, bird as valid values
$isValidPetType = $petTypeParam == "dog" || $petTypeParam == "cat" || $petTypeParam == "bird";

//Checks if the selected province ($province) exists as a key within the province array ($provinceArray)
$isValidProvince = key_exists($province, $provinceArray);

//Moves the complex logic for showing the form into the server block to keep html section clean
$showForm = !$isPosted || ($isPosted && (!$isValidPostParam || !$isValidAgreeToTerms || !$isValidPetType || !$isValidProvince));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forms and SuperGlobal Examples</title>

    <style>
        span.error
        {
            color: red;
            display: block;
        }
    </style>
</head>

<body>
<h1>Forms and SuperGlobal Examples</h1>

<?php if($showForm) { ?>       <!-- Checks if page was posted or not. Hides form if it was posted -->
<div>
    <h2>Simple Form Example</h2>
    <!-- MINICISE 2: Output the user type value into the text so that the textbox retains the text after posting/submitting -->
    <form method="post" action="?testurlparam=TestTest">
        <div>
            <label>Test Form Param:
            <input type="text" value="<?= htmlentities($postParam)?>" name="testpostparam"/></label>     <!-- CLEAN THE VALUE OUTPUT /w htmlentities -->
            <?php if(!$isValidPostParam)    //Checks if a post is valid, only shows the error if it is invalid
            { ?>
                <span class="error">This field can not be empty or all spaces</span>
            <?php } ?>
        </div>

        <div>
            <label>Agree To Terms:
                <!-- Minicise 3 - Add code so that if the user checks the checkbox and posts/submits the form, the checkbox will remain checked -->
                <input type="checkbox" value="1" name="agreetoterms" <?= $isValidAgreeToTerms ? "checked" : "" ?>/>
            </label>
            <?php if($isPosted && !$isValidAgreeToTerms) { ?>
            <span class="error">You must agree to the terms of the website</span>
            <?php } ?>
        </div>

        <div> <!-- Radio Button Examples -->
            <label>Pet Type</label>
            <label><input type="radio" name="pettype" value="dog" <?= $petTypeParam == "dog" ? "checked" : "" ?>/>Dog</label>

            <label><input type="radio" name="pettype" value="cat" <?= $petTypeParam == "cat" ? "checked" : "" ?>/>Cat</label>
            <label><input type="radio" name="pettype" value="bird" <?= $petTypeParam == "bird" ? "checked" : "" ?>/>Bird</label>
            <?php if($isPosted && !$isValidPetType) { ?>
                <span class="error">You must select a pet type</span>
            <?php } ?>
        </div>


        <div> <!-- Select Box/Drop Down List Example-->
            <label>Province:
                <select name="province">

                    <!-- Should always give users an invalid value to force them to select a valid option -->
                    <option value="0">Select a province</option>
                    <?= $provinceOptionsHTML ?>
<!--                    <option value="ab">Alberta</option>-->
<!--                    <option value="mb">Manitoba</option>-->
<!--                    <option value="sk">Saskatchewan</option>-->
                </select>
            </label>
            <?php if($isPosted && !$isValidProvince) { ?>
                <span class="error">You must select a province</span>
            <?php } ?>
        </div>
        <div>
            <input type="submit"/>
        </div>
    </form>
</div>
<?php } ?>

<div>
    <h2>CAUTION: never output user inputs without cleaning first</h2>
    <p>POST testpostparam value: <?= ($isValidPostParam) ? htmlentities($postParam) : "Invalid entry"?></p>
    <p>GET testurlparam value: <?= htmlentities($urlParam)?></p>
</div>

<div>
    <h2>PHP Debug Info - DO NOT DO THIS ON ACTUAL SITES</h2>
    <?php
    echo 'VARDUMP of $_POST';
        var_dump($_POST);
    echo 'VARDUMP of $_GET';
        var_dump($_GET);
    ?>

</div>
</body>
</html>