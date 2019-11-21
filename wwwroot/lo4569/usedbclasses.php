<?php
/**************************************
 * File Name: usedbclasses.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-18
 * Project: CWEB280
 *
 *
 **************************************/

//Include/Require all classes we need to use
use ORM\Repository;

require_once '../../lib/Student.php';
require_once '../../lib/ORM/Repository.php';

//$names = ["Adam","Bob","Charlie","Darcy","Emily","Fred","Gabby","Howard","Ida","Joe","Kevin","Leonard","Mel","Norma","Oscar","Peter","Quinn","Roger","Sarah","Tim","Uganda","Victor","Will","Xavier","Yon","Zed", "Anthony","Bub","Catherine","David","Elvis","Frank","Greg","Holden","Issac","Jack","Katherine","Louis","Michael","Ned","Olivia","Potter","Quinton","Ricky","Steve","Tom","Urmum","Victoria","Wesley","Xander","Zack"];
//$drank = ["Cola","Coke","Root","Beer","Soda","Cream","Fizzy","Bubbly","Drank","Drink","Berry","Orange","Pop"];
//for($i = 0; $i < 75 ; $i++)
//{
//    $student = new Student();
//    $student->id=rand(1001,9999);
//    $student->familyName=$drank[rand(0,sizeof($drank) - 1)];
//    $student->givenName=$drank[rand(0,sizeof($drank) - 1)];
//    $student->preferredName =$drank[rand(0,sizeof($drank) - 1)];
//    $student->userName = strtolower($student->familyName) . rand(1000,9999);
//    $repo = new ORM\Repository('../../db/students.db');
//    $result = $repo->insert($student);
//    $repo->close();
//}


////Create Student object
//$student = new Student();
//var_dump($student->validate());

////Fill Student object's properties with values
//$student->id=rand(1001, 9999);
//$student->familyName='Object';
//$student->givenName = 'Entity';
//$student->preferredName = 'ORM';
//$student->userName = strtolower($student->familyName) . rand(1000, 9999);

//var_dump($student);
//var_dump($student->validate());


//$student2 = new Student();
//$student2->id=rand(1001, 9999);
//$student2->familyName='Smith';
//$student2->givenName = 'Bob';
//$student2->preferredName = 'Boy';
//$student2->userName = strtolower($student2->familyName) . rand(1000, 9999);


////Create a db/repo object that will run SQL commands
$repo = new ORM\Repository('../../db/students.db');
//
////Call the insert function passing the student object
//$result = $repo->insert($student);
//
////Best practice close the repo/db
//$repo->close();
//var_dump($result);
//echo $repo->getLastStatement();
//

//$repo = new ORM\Repository('../../db/students.db');
//$result = $repo->insert($student2);
////Best practice close the repo/db
//$repo->close();

$studentSelect = new Student();
$studentSelect->familyName='%a%';
$studentSelect->givenName='%te%';


$repo = new ORM\Repository('../../db/students-Alt.db');
$result = $repo->select($studentSelect, true);
$repo->close();
echo $repo->getLastStatement();
var_dump($result);




//Fix all of your problems