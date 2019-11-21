<?php
/**************************************
 * File Name: Noodle.php
 * User: Kyle Wei - cst234
 * Date: 2019-11-1
 * Project: CWEB280 Assignment 2
 *
 * Noodle object class
 *
 **************************************/

require_once 'Entity.php';

class Noodle extends cst234cweb280a2\Entity
{
    public $noodleID;       //PK for the noodles
    public $brand;          //Brand of the noodle
    public $flavour;        //Noodle flavour
    public $instant;        //Instant noodle or not
    public $nationality;    //country of origin

    public function Noodle()
    {
        $this->pkName = 'noodleID';
        $this->propTypes['noodleID'] = 'INTEGER PRIMARY KEY AUTOINCREMENT';
        $this->propTypes['brand'] = 'VARCHAR(30)';
        $this->propTypes['flavour'] = 'VARCHAR(20)';
        $this->propTypes['instant'] = 'BOOLEAN';
        $this->propTypes['nationality'] = 'VARCHAR(20)';

    }
}

