<?php
/**************************************
 * File Name: Student.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-18
 * Project: CWEB280
 *
 *
 **************************************/

//require and require_once willy copy/paste the code from another php file into this file
require_once 'ORM\Entity.php';

//Extends is similar to inheritance in other languages
class Student extends ORM\Entity
{
    /***
     * @return array - Array of all errors relating to studentID. Empty if the studentID value is valid
     */
    public function validate_studentID()
    {
        $validationResult = [];
        if(!is_int($this->studentID) || $this->studentID <= 0)
        {
            $display = $this->getDisplayName('studentID');
            $validationResult['studentID'] = $display . ' must be an integer greater than 0';
        }
        return $validationResult;
    }
    public $studentID;


    /***
     * @return array - Array of all errors relating to Family Name. Empty if familyName is valid
     */
    public function validate_familyName()
    {
        $validationResult = [];
        $display = $this->getDisplayName('familyName');
        if(empty(trim($this->familyName)))
        {
            $validationResult['familyName'] = $display . ' is required and cannot be all spaces';
        }
        else
        {
            if(strlen($this->familyName) > 50)
            {
                $validationResult[] = $display . ' Name maximum length is 50 characters';
            }
        }
        return $validationResult;
    }
    public $familyName;

    public function validate_givenName()
    {
        $validationResult = [];
        $display = $this->getDisplayName('givenName');

        if(empty(trim($this->givenName)))
        {
            $validationResult['givenName'] = $display . ' is required and cannot be all spaces';
        }
        else
        {
            if(strlen($this->givenName) > 50)
            {
                $validationResult[] = $display . ' maximum length is 50 characters';
            }
        }
        return $validationResult;
    }
    public $givenName;

    //Minicise 29 - Create validator for the option Preferred Name but still limited to 50 characters
    public function validate_preferredName()
    {
        $validationResult = [];
        $display = $this->getDisplayName('preferredName');

        if(strlen($this->preferredName) > 50)
        {
            $validationResult['preferredName'] = $display . ' cannot be longer than 50 characters';
        }
        return $validationResult;
    }
    public $preferredName;


    public function validate_userName()
    {
        $validationResult = [];
        $display = $this->getDisplayName('userName');

        if(empty($this->userName))
        {
            $validationResult['userName'] = $display . ' cannot be empty';
        }
        if(strlen($this->userName) > 54)
        {
            $validationResult['userName'] = $display . ' cannot be longer than 54 characters';
        }

        return $validationResult;
    }
    public $userName;

    /**
     * Student constructor.
     */
    public function Student()
    {
//        $this->pkName = 'studentID';    //Tell entity parent that this child is using studentID as the primary key

        $this->addColumnDefinition('studentID','INTEGER','PRIMARY KEY');
        $this->displayNames = [
            'studentID'=>'Student ID',
            'familyName'=>'Family Name',
            'givenName'=>'Given Name',
            'preferredName'=>'Preferred Name',
            'userName'=>'User Name'
        ];
    }
}