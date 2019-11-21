<?php
/**************************************
 * File Name: ChocolateBar.php
 * User: Kyle Wei - cst234
 * Date: 2019-11-1
 * Project: CWEB280 Assignment 2
 *
 * Chocolate Bar object class
 *
 **************************************/

require_once 'Entity.php';

class ChocolateBar extends cst234cweb280a2\Entity
{
    public $chocoID;        //PK for the chocolate bars
    public $brand;          //Chocolate bar brand
    public $flavour;        //Flavour of the chocolate bar
    public $amountLeft;     //How much of the chocolate bar is remaining
    public $lethality;      //Probability that someone will die from eating or being around this bar
    public $rating;         //Rating of the bar

    public function ChocolateBar()
    {
        $this->pkName = 'chocoID';
        $this->propTypes['chocoID'] = 'INTEGER PRIMARY KEY AUTOINCREMENT';
        $this->propTypes['brand'] = 'VARCHAR(30)';
        $this->propTypes['flavour'] = 'VARCHAR(20)';
        $this->propTypes['amountLeft'] = 'DOUBLE(3,2)';
        $this->propTypes['lethality'] = 'INTEGER NOT NULL';
        $this->propTypes['rating'] = 'INTEGER';

    }
}