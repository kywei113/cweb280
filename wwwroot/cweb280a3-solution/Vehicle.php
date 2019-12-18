<?php
/**************************************
 * File Name: Vehicle.php
 * User: ins226
 * Date: 2019-11-28
 * Project: CWEB280
 **************************************/
require_once 'ORM/Entity.php';

class Vehicle extends ORM\Entity
{
    /***
     * @return array - all errors - empty array if value in vehicleID is valid
     */
    public function validate_vehicleID(){
        $validationResult = [];
        if(!empty($this->vehicleID) &&  (!is_int($this->vehicleID)|| $this->vehicleID<1) ){
			$label= $this->getDisplayName('vehicleID');
            $validationResult['vehicleID']="$label must be an integer greater than 0 or empty";
        }
        return $validationResult;
    }
    public $vehicleID;

    public function validate_make(){
        $validationResult = [];
		$label= $this->getDisplayName('make');
        if(empty(trim($this->make))){$validationResult['make']="$label is required and can not be all spaces";}
        elseif(strlen($this->make)>25){$validationResult['make']="$label maximum length is 25 characters";}
        return $validationResult;
    }
    public $make;

    public function validate_model(){
        $validationResult = [];
		$label= $this->getDisplayName('model');
        if(empty(trim($this->model))){$validationResult['model']="$label is required and can not be all spaces";}
        elseif(strlen($this->model)>25){$validationResult['model']="$label maximum length is 25 characters";}
        return $validationResult;
    }
    public $model;

    public function validate_type(){
        $validationResult = [];
		$label= $this->getDisplayName('type');
        if(!in_array($this->type,['Sedan', 'Compact', 'Cross Over', 'Truck'])){$validationResult['type']="$label must be Sedan, Compact, Cross Over or Truck";}
        return $validationResult;
    }
    public $type;

    public function validate_year(){
        $validationResult = [];
		$label= $this->getDisplayName('year');
        $maxYear = intval(date("Y"))+1;
        $year = intval($this->year);
        //NOTE: minimum year was not asked for in the assignment, but I included a minimum check here just for fun!
        if(!$year || $year>$maxYear || $year<1886){$validationResult['year']="$label is required must be between 1886 and $maxYear";}
        return $validationResult;
    }
    public $year;

    /**
     * vehicle constructor.
     */
    public function __construct()
    {
        //add display names for error messages - in case we change the display name later it will be easier to do so if all the display name are in one place
		$this->displayNames=['vehicleID'=>'Vehicle ID','make'=>'Make','model'=>'Model', 'type'=>'Type','year'=>'Year'];
		
        //add columns , NOTE: will also set pkName and determine auto-incrementing fields
        $this->addColumnDefinition('vehicleID','integer','primary key autoincrement');
        $this->addColumnDefinition('make','varchar(25)','not null');
        $this->addColumnDefinition('model','varchar(25)','not null');
        $this->addColumnDefinition('type','varchar(10)','not null');
        //CORRECTION: year was not required in the assigment - but I will either because the example i showed you year was required
        $this->addColumnDefinition('year','integer','not null');

    }

}