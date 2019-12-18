<?php
namespace ORM;

spl_autoload_register(function($entityClassName){
    require_once '../' . $entityClassName . '.php';
});

class Repository extends \SQLite3
{
    private $lastStatement;

    public function getLastStatement()
    {
        return $this->lastStatement;
    }

    public function createTables($entities)
    {
        $results=[];
        foreach ($entities as $entity){
            $tableName = $entity->getClassName();
            $this->lastStatement = "DROP TABLE IF EXISTS $tableName";
            if(!$this->exec($this->lastStatement)){
                $results[$tableName] = 'ERROR: ' . $this->lastStatement;
            }else{
                $columnString = implode(',' . PHP_EOL, $entity->getColumnDefinitions());
                $this->lastStatement = "CREATE TABLE $tableName ($columnString)";
                $results[$tableName] = $this->exec($this->lastStatement) ? 1 : 'ERROR: ' . $this->lastStatement;
            }
        }
        return $results;
    }

    /***
     * @param $entity - Entity child that contains the properties/fields and values to be inserted into the database
     * @return int - RULE: On validation Error return 02, on Statement error return -1, on Execution error return 0, on Success return 1
     */
    public function insert($entity)
    {
        //BEST PRACTICE: Validate the entity before calling the insert function
        if(count($entity->validate()))
        {
            return -2;  //RULE: On validation error return -2
        }

        //Get the table name and properties (with values) from the entity
        $tableName = $entity->getClassName();
        $autoFields = $entity->getAutoIncrementColumnDefinitions(); //Get all fields that contain AUTOINCREMENT in their column definition
        $filteredProperties = array_diff_key(get_object_vars($entity), $autoFields ); //Filter out the AutoIncrement fields from the properties array

        //Determine the field list and placeholders for the prepared statement
        $fieldString = implode(',', array_keys($filteredProperties));
        $qMarkString = str_repeat('?,', count($filteredProperties)-1) . '?';

        //Preparing the INSERT statement - remember the rules
        $this->lastStatement = "INSERT INTO $tableName ($fieldString) VALUES($qMarkString)";
        $stmt=$this->prepare($this->lastStatement);
        if(!$stmt){return -1;}  //RULE: On Statement error return -1

        //Bind values for the placeholders in the INSERT statement
        $i = 1;
        foreach($filteredProperties as $field=>$val){
            $this->lastStatement .= " [$i:$field=$val] ";   //RULE: Save the bound values in the lastStatement for debugging
            $stmt->bindValue($i++,$val,$entity->getBindType($field));
        }


        //Execute statement - remember the rules
        $result = $stmt->execute();
        if($result) //Entity data successfully saved to database
        {
            $autoID = $this->lastInsertRowID(); //Get the last auto incremented value from the database
            foreach (array_keys($autoFields) as $field) //Loop through autofields
            {
                $entity->$field = $autoID;  //Set their values to the last auto from DB
            }
        }
        $stmt->close(); //BEST PRACTICE: Close statement after we are done with the result
        return $result ? 1: 0;      //RULE: On execute error return 0, on success return 1
    }

    /**
     * Comment all of SELECT function for next class
     */
    /***
     * Selects a record or set of records from the table based on a provided object and its properties.
     * Can be set to allow for wild-card strings to search for a wider range of records
     * @param $entity  - Object to derive property criteria from
     * @param bool $inclusive - Determines whether to use OR or AND for specific or non-specific results
     * @return array|int    - Returns either an array of results or a status code
     */
    public function select($entity, $inclusive = false)
    {
        $tableName = $entity->getClassName();           //Gets the table name to target
        $properties = get_object_vars($entity);         //Get properties from the incoming object. Select will use these as search criteria

        $fieldString = implode(',', array_keys($properties));       //Breaks apart the properties and builds a comma separated string
        $filteredProperties = array_filter($properties);                    //Filters out null value properties

        //MINICISE 34: Alter the Where clause so it can have wild cards in the values EXAMPLE - '%Sm%' will match 'Smith' and 'Smile'
        $whereString = empty($filteredProperties)? ' 1=1 ': implode($inclusive ? ' LIKE ? OR ' : ' LIKE ? AND', array_keys($filteredProperties)) . ' LIKE ?';   //Creates a string to be used as a WHERE clause in the SELECT statement. Depending if it's to be inclusive or not, will use LIKE or AND for criteria

        $this->lastStatement = "SELECT $fieldString FROM $tableName WHERE $whereString";    //Prepares the SELECT statement, builds it from the strings created above
        $stmt=$this->prepare($this->lastStatement);
        if(!$stmt){return -1;}                                          //If stmt preparation returns false, returns a -1 status code

        $i = 1;
        foreach($filteredProperties as $field=>$val)
        {                   //Cycles through filtered properties and binds values to them.
            $this->lastStatement .= " [$i:$field=$val] ";
            $stmt->bindValue($i++,$val,$entity->getBindType($field));
        }

        $result = $stmt->execute();         //Executes the statement and receives a result based on whether execution was successful or not
        if(!$result){
            $stmt->close();                 //If result returns as false, closes the statement and returns status code 0
            return 0;
        }

        $entityArray=[];                                                                //Parses the results of the statement into an array of objects
        while ($tableRow = $result->fetchArray(SQLITE3_ASSOC))
        {
            $entityArray[] = (new $tableName())->parseArray($tableRow);
        }
        $stmt->close();
        return $entityArray;                //Returns the array of table records
    }



    /***
     * MINICISE - CST234
     * @param $entity - Entity with properties and values to identify a target record, and values to update the record with
     * @return int - Status codes indicating method success or various errors.
     *      Validation Error returns -2,
     *      Statement Error returns -1,
     *      Execution Error returns 0,
     *      Success returns 1
     */
    public function update($entity)
    {
        if(count($entity->validate())){return -2;}      //Validates the incoming entity object. Returns -2 if validation error is found
       
        $properties = get_object_vars($entity);         //Gets properties and corresponding values from Entity
        $pkName = $entity->getPkName();                 //Determines the name of the entity's primary key

        unset($properties[$pkName]);                    //Unsets the entity's primary key value
        $setString = implode('=?, ',array_keys($properties)).'=?';  //Creates string of property names and placeholders


        $this->lastStatement = "UPDATE {$entity->getClassName()} SET $setString WHERE $pkName=?";    //Structures UPDATE string with Table, Fields with placeholders, and primary key criteria
        $stmt=$this->prepare($this->lastStatement);     //Prepares the statement
        if(!$stmt){return -1;}      //If statement preparation fails, return -1

        $i = 1;
        foreach($properties as $field=>$val){
            $this->lastStatement .= " [$i:$field=$val] "; //Saves bound properties and values in lastStatement for debugging
            $stmt->bindValue($i++,$val,$entity->getBindType($field));   //Binds values to properties and gets appropriate data type for the field
        }

        $this->lastStatement .= " [$i:$pkName={$entity->$pkName}] ";        //Adds primary key property/value to lastStatement
        $stmt->bindValue($i,$entity->$pkName,$entity->getBindType($pkName));    //Binds primary key value to final placeholder


        $result = $stmt->execute();     //Executes statement
        $stmt->close();             //Closes the statement
        return $result ? 1: 0;          //On execution error returns 0, on success returns 1
    }

    public function delete($entity){
        $pkName = $entity->getPkName();

        $this->lastStatement="DELETE FROM {$entity->getClassName()} WHERE $pkName=?";
        $stmt = $this->prepare($this->lastStatement);
        if(!$stmt){return -1;}

        $this->lastStatement .= " [1:$pkName={$entity->$pkName}] ";
        $stmt->bindValue(1,$entity->$pkName,$entity->getBindType($pkName));

        $result =$stmt->execute();
        $stmt->close();
        return $result ? 1: 0;
    }

}