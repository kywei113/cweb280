<?php
/**************************************
 * File Name: setup.php
 * User: Kyle Wei - cst234
 * Date: 2019-11-01
 * Project: CWEB280
 * CWEB280 Assignment 2
 * Setup DB PHP file
 * Contains code to:
 *      Create a repository object and a database to operate on.
 *      Create a Cookie, Chocolate Bar, and Noodle entity
 *      Create relevant tables in the DB
 *      Populate the Cookie, Chocolate Bar, and Noodle tables with 50 entries each
 *      Run Mass SELECT statements on each table
 *      Update a specific noodle entry
 *      Delete a specific chocolate bar entry
 *      Execute a mass deletion on the Cookie table
 **************************************/

//Cookies, Chocolate Bars, Noodles
require_once 'ChocolateBar.php';
require_once 'Cookie.php';
require_once 'Noodle.php';
require_once 'Repository.php';

$cookie = new Cookie();
$chocoBar = new ChocolateBar();
$noodle = new Noodle();

//Array of the three entity objects. Left without properties
$entities = [$cookie, $chocoBar, $noodle];

$repo = new cst234cweb280a2\Repository('a2database.db');

/***
 * Runs the CREATE TABLE function. Comment it out if you don't want to drop and recreate tables
 */
$createResults = $repo->createTables($entities);

/************************************************************
 * Run this populateTables to generate 50 noodles, cookies, and chocolate bars
 * FUNCTION TAKES SOME TIME TO RUN. DO NOT RUN IT EVERY TIME.
 * FUNCTION TAKES SOME TIME TO RUN. DO NOT RUN IT EVERY TIME.
 * FUNCTION TAKES SOME TIME TO RUN. DO NOT RUN IT EVERY TIME.
 * **********************************************************/
//populateTables($repo);

/**Functions to run different statements. For testing and for fun*/
//runSelect($repo);
//runUpdate($repo);
//runDelete($repo);
//runDeleteMass($repo);

//Runs parseArray and generates a cookie object
parseEntityFromArray();

/***
 * Function parses a cookie object from an array of properties. Also attempts to parse a cookie object with a non-array object
 */
function parseEntityFromArray()
{
    $propArray = ['cookieID'=> 459, 'flavour'=>'Soy Sauce', 'type'=>null,'delicious'=>0];
    $parseCookie = (new Cookie())->parseArray($propArray);


    $failedParseCookie = (new Cookie())->parseArray("You get nothing");

    var_dump($parseCookie);
    var_dump($failedParseCookie);
}

/***
 * Function to run SELECT on all Chocolate Bars, Cookies, and Noodles
 * @param $repo - Repository to use and DB to target
 */
function runSelect($repo)
{
    //    Var Dumping each entity type to test select
    var_dump($repo->select(new ChocolateBar()));
    var_dump($repo->select(new Cookie()));
    var_dump($repo->select(new Noodle()));
}

/***
 * Function to create a new Chocolate Bar object with just an ID. Deletes that ID in the DB
 * @param $repo - Repository to use and DB to target
 */
function runDelete($repo)
{
    $delBar = new ChocolateBar();
    $delBar->chocoID= 15;
    $repo->delete($delBar);
}

/***
 * Function to create a new Noodle object, and update an existing ID in the db
 * @param $repo - Repository to use and DB to target
 */
function runUpdate($repo)
{
    $updNoodle = new Noodle();
    $updNoodle->noodleID = 17;
    $updNoodle->brand = 'FLAVOURS OF THE SUN';
    $updNoodle->flavour = 'COSMIC DUST';
    $updNoodle->instant = 1;
    $updNoodle->nationality = 'OUT OF THIS WORLD';
    $repo->update($updNoodle);
}

/***
 * For funsies function.
 * Function to create a cookie object, and delete all entries in the cookie table relating to each field
 * Specified it to delete all Pork flavoured cookies, Sugar cookies, and Cookies with a FALSE for deliciousness
 * Remaining table will have no pork or sugar cookies, and only cookies that are allegedly delicious
 * @param $repo - Repository to use and DB to target
 */
function runDeleteMass($repo)
{
    $delMassCookie = new Cookie();
    $delMassCookie->flavour = "Pork";
    $delMassCookie->type = "Sugar";
    $delMassCookie->delicious = 0;

    $repo->deleteMass($delMassCookie, "flavour");
    $repo->deleteMass($delMassCookie, "type");
    $repo->deleteMass($delMassCookie, "delicious");
}

/***
 * Function to generate 40 noodle, cookie, and chocolate bar objects and insert them into their tables
 * @param $repo - Repository to use and DB to target
 */
function populateTables($repo)
{
    $brands = ['Oreo', 'Coffee Crisp','Ichiban','Nongshim','Kraft','Kitkat','Pilsbury','Voortman','Nissin','Mr.Noodle','Generic','Loblaw','Dad\'s', 'Baby Ruth', 'Big Turk'];
    $flavours = ['Chocolate','Fudge','Chicken','Curry','Sriracha','Peanut Butter',
        'Mint Chocolate','Oatmeal Raisin','Beef','Pork','Yakisoba','Squid Ink','Strawberry','Green Tea','Plain Mochi','Licorice','Whyyyyyyyy'];
    $nationalities = ['United State','Canada','Italy','England','Wales','Scotland','Ireland','Germany','France','Belgium','Denmark','The Netherlands','Mexico','China','Japan','Vietnam','Narnia'];
    $types = ['Sugar','Cheese Cake','Carrot','Brownie','Hot Fudge','Sour Dough','Dough Dough','Dough Boi','One Tuff Cookie','Mystery Meat','Not-Human','Not-A-Household-Pet','Beef','Pork','Candy'];

    $cookies = [];
    $chocoBars = [];
    $noodles = [];

    for($i = 0; $i < 50; $i++)
    {
        $cookie = new Cookie();
        $cookie->flavour = $flavours[rand(0, sizeof($flavours)-1)];
        $cookie->type = $types[rand(0, sizeof($types)-1)];
        $cookie->delicious = rand(0,1);

        $cookies[] = $cookie;

        $cBar = new ChocolateBar();
        $cBar->brand = $brands[rand(0, sizeof($brands)-1)];
        $cBar->flavour = $flavours[rand(0, sizeof($flavours)-1)];
        $cBar->amountLeft = rand(0.0, 100.0) / 100;     //Gets a decimal value between 0.0 and 1.0
        $cBar->lethality = rand(0, 100);
        $cBar->rating = rand(0, 10);

        $chocoBars[] = $cBar;

        $noodlyWoodlies = new Noodle();
        $noodlyWoodlies->brand = $brands[rand(0,sizeof($brands)-1)];
        $noodlyWoodlies->flavour = $flavours[rand(0, sizeof($flavours)-1)];
        $noodlyWoodlies->instant = rand(0, 1);
        $noodlyWoodlies->nationality = $nationalities[rand(0,sizeof($nationalities)-1)];

        $noodles[] = $noodlyWoodlies;
    }

    foreach($cookies as $cookie)
    {
        $repo->insert($cookie);
    }

    foreach($noodles as $noodle)
    {
        $repo->insert($noodle);
    }
    foreach($chocoBars as $cBar)
    {
        $repo->insert($cBar);
    }
}






