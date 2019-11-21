<?php
/**************************************
 * File Name: structures.php
 * User: cst234
 * Date: 2019-09-9/4/2019
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/

//Declare variables
//Perform calculations
//Create objects
//Connect to database
//etc...

//Example of using built-in PHP functions
//Built-in PHP functions are generally faster as they are compiled, not interpreted
$rnd = rand(1, 10);

//Conditional statement - determine if the random number is odd or even
//$msg = ($rnd % 2 == 0) ? "Random number is <span style = 'color: green'>even</span>" : "Random number is <span style = 'color: red'>odd</span>";
$msg = ($rnd % 2 == 0) ? "<style>span {color: green;}</style><span>Random number is even</span>" : "<style>span {color: red;}</style><span>Random number is odd</span>";
//if($rnd % 2 == 0)
//{
//    $msg = "Random number is <span style = 'color: green'>even</span>";
//
//}
//else
//{
//    $msg = "Random number is <span style = 'color: red'>odd</span>";
//}
$isEven = $rnd % 2 == 0;


//Use while to calculate the sum of numbers
$sumWhile = 0; //Stores the running total in a variable
$sumWhileOut = "";
$count = 1; //Stores the number of times through the loop in a variable

//While loop structure - governor - While count is less than the random number
while($count < $rnd)
{
        $sumWhile += $count;
        $count++;   //Increment count so we can leave the loop eventually
        $sumWhileOut .= "<li>$sumWhile</li>";   //PHP strings with double quotes will evaluate variables before calculating strings
}

$sumFor = 0;
$sumForOut = "";
//Write the above code in FOR LOOP form
for($i = 1; $i < $rnd; $i++)
{
    $sumFor += $i;
    $sumForOut .= "<li>$sumFor</li>";
}

//Switch statements - Use Switch statements when many conditions exist
//Case statements are very flexible in PHP, can even use a Conditional statement
switch($rnd)
{
    case(1):
        $switchMsg = "The random number is 1 - the loneliest number";
        break;
    case($rnd <= 3):
        $switchMsg = "The random number is between 2 and 3 -- $rnd";
        break;
    case($rnd > 3):
        $switchMsg = "The random number is greater than 3 -- $rnd";
        break;
    default:    //Always set a default even if all cases are covered
        $switchMsg = "Yeh dun fackered ahp";
        break;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP Structures Examples</title>
</head>

<body>
<h1>PHP Structures Examples</h1>

<div>
    <h2>Use if statement to determine if the number is odd or even</h2>
<!--    <span>--><?//= $msg ?><!-- - --><?//= $rnd ?><!--</span>-->
<!--    --><?//= $msg ?>
    <!-- Minicise 1: Output if the random number is odd or even and change the color of the text depending on the result.
                     If the number is odd, color the text . If the number is even, color the text . -->

<!--    --><?php //if($isEven) { ?>
<!--    <span style="color:green"> The random number is EVEN</span> - --><?//= $rnd ?>
<!--    --><?php //} else { ?>
<!--    <span style="color: red">The random number is ODD</span> - --><?//= $rnd ?>
<!--    --><?php //} ?>

    <span style="color: <?= $isEven ? "green" : "red" ?>"> The random number is <?= $isEven ? "EVEN" : "ODD" ?> - <?= $rnd ?></span>
</div>

<div>
    <h2>Use While loop to generate content</h2>
<!--    <span>--><?//= $sumWhile ?><!--</span>-->
    <span><?= $sumWhileOut?></span>

    <h2>With For</h2>
    <span><?= $sumForOut?></span>
<!--    <span>--><?//= $sumTwo ?><!--</span>-->

    <!-- Output the value of each sum after each iteration through the loop -->
</div>

<div>
    <h2>Switch-a-roo</h2>
    <p><?= $switchMsg?></p>
</div>
</body>
</html>