<?php
/**************************************
 * File Name: Cookie.php
 * User: Kyle Wei - cst234
 * Date: 2019-11-1
 * Project: CWEB280 Assignment 2
 *
 * Cookie Object Class
 *
 **************************************/

require_once 'Entity.php';

class Cookie extends cst234cweb280a2\Entity
{
    public $cookieID;       //PK for the cookie
    public $flavour;        //Flavour of the cookie (chocolate chip, caramel)
    public $type;           //Dough type (sour dough, sugar)
    public $delicious;      //Whether the cookie is delicious or not

    public function Cookie()
    {
        $this->pkName = 'cookieID';
        $this->propTypes['cookieID'] = "INTEGER PRIMARY KEY AUTOINCREMENT";
        $this->propTypes['flavour'] = "VARCHAR(20)";
        $this->propTypes['type'] = "VARCHAR(20)";
        $this->propTypes['delicious'] = "BOOLEAN";
    }
}