<?php
/**************************************
 * File Name: Entity.php
 * User: Kyle Wei - cst234
 * Date: 2019-11-1
 * Project: CWEB280 Assignment 2
 *
 *  The base class for an Entity object.
 * Children of this class will be sent to the Repository as parameters
 * Entities will also have validation rules for the entity properties
 *      This means that entities will check the data coming from the user before storing in the database
 *      and the other way around too (validate information coming from the database before displaying it to the user)
 **************************************/

namespace cst234cweb280a2;

abstract class Entity
{
    protected $pkName = 'id'; //Set a default for primary key to be 'id' for al new entity children
    protected $propTypes = [];  //Array of constraints to be applied to each field. Set within child entity constructors

    /**
     * @return string - The property name that is the primary key/unique identifier for the entity
     */
    public function getPkName(): string
    {
        return $this->pkName;       //Gets the value of pkName, usually changed by child objects
    }

    public function getClassName()
    {
        return get_class($this);    //Returns the class name of the child object
    }

    public function getPropTypes()
    {
        return $this->propTypes;    //Returns the array of property types for the object
    }

    /***
     * Checks an incoming array against the keys present in the current entity
     * If keys (other than propTypes & pkName) match, keys with values are entered into a new entity and it is returned
     * @param $array - Associative array that has the same keys as the entity has properties
     * @return Entity|int
     */
    public function parseArray($array)
    {
        //Checks first array against other ones, returns an array of values in first array that are not present in others
        $currentKeys = get_object_vars(($this));
        unset($currentKeys['propTypes']);       //Prop types and pkName aren't fields that are set by incoming arrays
        unset($currentKeys['pkName']);          //Prop types and pkName aren't fields that are set by incoming arrays

        //DONE: Ensure the array is assoc has the correct keys
        //https://www.php.net/manual/en/function.array-diff.php
        $keyDiff = array_diff(array_keys($array), array_keys(get_object_vars($this)));

        if(sizeof($keyDiff) > 0)     //checks if more than 1 difference exists between incoming array and entity keys
        {
            return -1;          //Returns -1 if more than one difference exists
        }
        else
        {
            foreach($array as $field=>$val)     //goes through each array property and assigns it to this object
            {
                if(isset($val))                //Only assigns properties that have values, ignores the rest
                {
                    $this->$field = $val;
                }
            }
            return $this;       //Returns the entity if no differences exist
        }
    }
}