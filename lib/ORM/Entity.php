<?php
namespace ORM;


abstract class Entity
{
    
    private $pkName = 'rowid';
    protected $displayNames =[]; 
    private $bindTypes=[];
    private $columnDefinitions=[];

    public function getPkName(): string
    {
        return $this->pkName;
    }

    public function getClassName(){
        return get_class($this);
    }

    public function getDisplayName($field){
        return isset($this->displayNames[$field])? $this->displayNames[$field] : $field;
    }

    public function getBindType($field){
        return isset($this->bindTypes[$field])? $this->bindTypes[$field] : SQLITE3_TEXT;
    }

    public function getColumnDefinitions()
    {
        return $this->columnDefinitions;
    }

    public function getAutoIncrementColumnDefinitions()
    {
        $result = preg_grep('/AUTOINCREMENT/i', $this->columnDefinitions);
        return $result? $result : [];
    }

    /***
     * Takes in a Field name, the desired type for the field, and a description/constraints on that field.
     * Function converts the type and constraints to upper case, and updates the entity's "pkName" attribute if the
     * incoming field is a Primary Key.
     *
     * Adds an entry in the Entity's ColumnDefinition array attribute composed of the Field Name, Type, and Constraints.
     * Adds an entry in the Entity's BindTypes array attribute with the enumerated SQLITE3 data type
     *
     * @param $field - Name of the field/column
     * @param $type - Data type for the field/column
     * @param string $description - Constraints to apply to the field/column
     */
    protected function addColumnDefinition($field,$type,$description=''){

        $type = strtoupper($type);                      //Converts argument data type to upper case
        $description = strtoupper($description);        //Converts argument constraints to upper case

        if(strpos($description,'PRIMARY KEY')>-1){$this->pkName=$field;}    //Checks if field constraints contain "Primary Key". Updates entity's pkName if PRIMARY KEY is found within the constraints

        $this->columnDefinitions[$field] = "$field $type $description";         //Adds an string of the field name, data type, and constraints to entity's columnDefinition array

        $bindType=null;                             //Placeholder variable for bindType enumeration

        //SQLITE3 enumerates multiple data types to the same internal data types, Switch checks the data type and enumerates based on that. https://www.sqlite.org/datatype3.html
        switch ( strtok($type,'(') )            //strtok delimits a string based on a given character, in this case it will split $type along any "(" characters in it
        {
            case 'INTEGER':                             //INTEGER, BIGINT, BOOLEAN all enumerate to SQLITE3 INTEGER
            case 'BIGINT':
            case 'BOOLEAN':
                $bindType = SQLITE3_INTEGER;            //Assigns SQLITE3_INTEGER to $bindType
                break;

            case 'REAL':                                //REAL, FLOAT, DECIMAL, DOUBLE all enumerate to SQLITE3 FLOAT
            case 'FLOAT':
            case 'DECIMAL':
            case 'DOUBLE':
                $bindType =  SQLITE3_FLOAT;             //Assigns SQLITE3_FLOAT to $bindType
                break;

            case 'BLOB':                                //BLOB data type used for binary data which might be stored as values within the database. https://www.sqlitetutorial.net/sqlite-php/blob/
                $bindType =  SQLITE3_BLOB;              //Allows for storage of files within a database; Assigns SQLITE3_BLOB to $bindType
                break;

            default:                                    //If any other data type is set, defaults to SQLITE3 TEXT
                $bindType =  SQLITE3_TEXT;              //Assigns SQLITE3_TEXT to $bindType
                break;
        }

        $this->bindTypes[$field] = $bindType;           //Adds an entry to the entity's bindType array, contains the SQLITE3 data type to use for the argument field
    }

    public function parseArray($array)
    {
         foreach (array_intersect_key($array, get_object_vars($this)) as $field=>$valFromArray){
            $this->$field = $valFromArray;
        }
        return $this;
    }

    public function validate()
    {
        $errorsArray =[];
        foreach (get_class_methods($this) as $functionName){
            if(strpos($functionName,'validate_')===0) {
                $errorsArray = array_merge($errorsArray, call_user_func([$this, $functionName]));
            }
        }
        return $errorsArray;
    }

}