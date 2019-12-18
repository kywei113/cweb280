<?php
require_once '../../lib/Vehicle.php';
require_once  '../../lib/ORM/Repository.php';


$repo = new ORM\Repository('vehicles.db');

$car = new Vehicle();

$repo->createTables([$car]);

$cars=[
    ["vehicleID"=>null,"make"=>"Aston Martin","model"=>"Sassy","type"=>"Sedan","year"=>2019],
    ["vehicleID"=>null,"make"=>"Bugati","model"=>"Veron","type"=>"Sedan","year"=>2018],
    ["vehicleID"=>null,"make"=>"Ford","model"=>"Fiesta","type"=>"Compact","year"=>2017],
    ["vehicleID"=>null,"make"=>"GMC","model"=>"Sierra","type"=>"Truck","year"=>2016],
    ["vehicleID"=>null,"make"=>"Toyota","model"=>"Prius","type"=>"Cross Over","year"=>2015]
];

foreach ($cars as $data){
    $repo->insert($car->parseArray($data));
    echo 'new id: ' . $car->vehicleID . PHP_EOL;
}
