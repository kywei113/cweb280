<?php
/**************************************
 * File Name: Repository.php
 * User: Kyle Wei - cst234
 * Date: 2019-11-4
 * Project: CWEB280 Assignment 2
 * This class will contain functions and some properties that will be used to run SQL commands on the database
 * The functions will receive entities as parameters - the entities contain data that will be bound to the prepared statements
 **************************************/

namespace cst234cweb280a2;

//RULE 1: Class name will be the same as the Table name in the database
//RULE 2: The properties within the entity child class must be the same as the table column names
//RULE 3: The properties Assoc array contains keys (column name) and values that will be inserted to the table
//RULE 4: All entity children will be in the lib folder 1 level above this ORM folder - the class is the same as the file name  IGNORED - Assignment states flat folder structure
//RULE 5: on Statement Failure returns -1, Result Failure returns 0, Success returns 1, -5 if invalid property passed into Mass Delete
//RULE 6: Always set the last statement property just before a prepare and execute - Makes debugging easier
//RULE 7: In Select, the properties and values in the entity child will be used as filters in the where clause

spl_autoload_register(function($entityClassName)
{
    require_once $entityClassName . '.php';
});

class Repository extends \SQLite3
{
    private $lastStatement;

    /**
     * Gets repository's last executed SQL statement for debugging
     * @return mixed
     */
    public function getLastStatement()
    {
        return $this->lastStatement;
    }

    /***
     * Handles Inserting a new entity into the database
     * @param $entity - Entity to be added
     * @return int - return status code based on result
     */
    public function insert($entity)
    {
        $tblInfo = $this->getTableInformation($entity);     //REDUCED - Helper method for getting pkName, tblName, and properties into the tblInfo array

        //DONE: Deal with auto-incrementing fields - the DB might through an error if trying to insert a value
        $pkName = $tblInfo['pkName'];
        if(array_key_exists($pkName, $tblInfo['properties']))      //Checks if pkName exists in the properties array
        {
            unset($tblInfo['properties'][$pkName]);                //If pkName exists, unset it
        }

        $fieldString = implode(',',array_keys($tblInfo['properties']));                         //Creates string of comma separated properties
        $qMarkString = rtrim(str_repeat('?,', count($tblInfo['properties'])), ',');     //Creates string of comma separated ?'s, trims the last one

        $sql = "INSERT INTO " . $tblInfo['tblName'] . " ($fieldString) VALUES($qMarkString)";         //SQL INSERT statement

        $stmt = $this->prepare($sql);       //Creates a Prepared Statement using the SQL string above
        $result = $this->bindExec($stmt, $sql, $tblInfo);  //REDUCED - Moved value binding and execution into a helper method bindExec

        $stmt->close();             //Closes the statement

        return $result ? 1: 0;      //Returns 1 on Successful execution, 0 on failure
    }


    /***
     * Handles Selecting all entities of a type, or a specific one.
     * @param $entity - Entity to be selected
     * @return array|int - Array of results or Integer Status code
     */
    public function select($entity)
    {
        $tblInfo = $this->getTableInformation($entity);     //REDUCED - Helper method for getting pkName, tblName, and properties into the tblInfo array

        $fields = implode(',', array_keys($tblInfo['properties'])); //Creating string of comma separated properties

        $whereString = $this->filterFields($tblInfo);           //Calls helper method to filter out fields and build whereString

        $sql = "SELECT $fields FROM " . $tblInfo['tblName'] . " WHERE $whereString";        //Creates SELECT statement to be used

        $this->lastStatement = $sql;            //Adds SQL to Entity's lastStatement

        $stmt = $this->prepare($sql);           //Creates a Prepared Statement from the SQL string
        if(!$stmt)
        {
            return -1;
        }

        $result = $this->bindExec($stmt, $sql, $tblInfo);       //REDUCED - Sends Statement, SQL and tblInfo into bindExec to bind values and execute statement
        if(!$result)                //Checks if bindExec failed,
        {
            return 0;
        }

        $resultSet = $this->parseRows($result, $tblInfo['tblName']);            //REDUCED - Calls helper method to parse result into an array

        $stmt->close();

        return $resultSet;                          //Returns array of results from SELECT statement
    }
    /***
     * Handles UPDATE statements for a given entity
     * @param $entity - Entity to update in the database
     * @return int - Status code based on success or failure
     */
    public function update($entity)
    {
        $tblInfo = $this->getTableInformation($entity);     //REDUCED - Helper method for getting pkName, tblName, and properties into the tblInfo array

        $pkName = $tblInfo['pkName'];                   //Assigning pkName from tblInfo for clarity later
        $idValue = $tblInfo['properties'][$pkName];   //store ID value for later

        unset($tblInfo['properties'][$pkName]);       //Removes the id field of from tblInfo['properties'] array

        $setFields = implode('=?, ', array_keys($tblInfo['properties']));       //Creates =? separated string of properties
        $setFields .= "=?";                                                           //Adds a =? to the last property

        $whereFields = $tblInfo['pkName'] . "=?";                                     //Gets the ID name of the entity and adds =?

        $sql = "UPDATE ". $tblInfo['tblName'] . " SET $setFields WHERE $whereFields";   //Creating Update statement
        $this->lastStatement = $sql;

        $tblInfo['properties'][$pkName] = $idValue;     //Re-adding pkName and idValue to end of the property array. Puts it in correct position to be bound into statement

        $stmt=$this->prepare($sql);     //Prepares the statement
        if(!$stmt)
        {
            return -1;
        }

        $result = $this->bindExec($stmt, $sql, $tblInfo);       //REDUCED - Calls helper method to bind and execute statement

        $stmt->close();

        return $result ? 1: 0;
    }

    /***
     * For funsies. Deletes all entities in a table which contain a property which matches the value of the incoming entity's property value
     * @param $entity   - Entity to be checked against
     * @param $property - Which property to target
     * @return int      - Success or not
     */
    public function deleteMass($entity, $property)
    {
        $tblInfo = $this->getTableInformation($entity);     //Helper method for getting pkName, tblName, and properties into the tblInfo array

        if(key_exists($property, $tblInfo['properties']))   //Check if incoming property exists in the entity properties
        {
            $whereString = $property . "=?";       //Using the primary key as the delete condition

            foreach($tblInfo['properties'] as $prop=>$val)  //Unsets properties that are not the target property
            {
                if($prop != $property)
                {
                    unset($tblInfo['properties'][$prop]);
                }
            }

            $sql = "DELETE FROM " . $tblInfo['tblName'] . " WHERE $whereString";    //Creating SQL DELETE statement

            $this->lastStatement = $sql;        //Assigning to lastStatement for debugging

            $stmt = $this->prepare($sql);       //Preparing the statement from $sql
            if(!$stmt)
            {
                return -1;
            }

            $result = $this->bindExec($stmt, $sql, $tblInfo);       //Sending stmt, sql, tblInfo to helper method to bind and execute statement

            $stmt->close();             //Closing the statement

            return $result ? 1: 0;      //Returning status code based on the result
        }
        else
        {
            return -5; //Status code for invalid property. Doesn't correspond with a property in the entity
        }

    }
    /***
     *  Handles DELETE statement. Currently only uses the cookieID of an incoming entity to delete.
     *  Can be implemented to delete multiple records if a property is passed in.
     * @param $entity - Entity to delete
     * @return int - Status code based on result
     */
    public function delete($entity)
    {
        $tblInfo = $this->getTableInformation($entity);     //REDUCED - Helper method for getting pkName, tblName, and properties into the tblInfo array

        $whereString = $tblInfo['pkName'] . "=?";       //Using the primary key as the delete condition

        $sql = "DELETE FROM " . $tblInfo['tblName'] . " WHERE $whereString";    //Creating SQL DELETE statement

        $this->lastStatement = $sql;        //Assigning to lastStatement for debugging

        $stmt = $this->prepare($sql);       //Preparing the statement from $sql
        if(!$stmt)
        {
            return -1;
        }

        $result = $this->bindExec($stmt, $sql, $tblInfo);       //REDUCED - Sending stmt, sql, tblInfo to helper method to bind and execute statement

        $stmt->close();             //Closing the statement

        return $result ? 1: 0;      //Returning status code based on the result
    }


    /***
     * Creates DROP and CREATE statements for entities within an incoming array.
     * Creates relevant fields and constraints for entity properties.
     * Returns an array of result strings indicating the status of each statement
     * @param $entities - Entity objects to be made into tables
     * @return array    - Array of results indicating status of statements
     */
    public function createTables($entities)
    {
        $resultArray = [];                                      //Array of result messages generated by each statement
        foreach($entities as $object)
        {
            $tblInfo = $this->getTableInformation($object);                 //Calling helper function to get pkName, tblName, properties, and field types
            $objectProps = [];                                              //Declaring objectProp array for use later. Will store field name and constraints

            $dropSQL = "DROP TABLE IF EXISTS " . $tblInfo['tblName'];     //Creating DROP statement for the given entity
            $dropResult = $this->prepExecClose($dropSQL);

            if($dropResult == -1)       //-1 code indicates preparation error
            {
                $resultArray[$tblInfo['tblName']]['drop'] = "Error: " . $tblInfo['tblName'] . " failed to drop. Preparation error.";    //Add message to result array indicating prep error
            }
            else
            {
                $resultArray[$tblInfo['tblName']]['drop'] = $tblInfo['tblName'] . " was successfully dropped";  //Add message to resultArray indicating successful drop

                foreach($tblInfo['properties'] as $prop=>$val)
                {
                    $objectProps[]= $prop . ' ' . $tblInfo['propTypes'][$prop];                 //Parsing out properties into a string along with field constraints. Add to objectProp array
                }

                $fieldString = rtrim(implode(',', $objectProps), ',');            //Delimit objectProp key/values with ','s. Trim off last comma

                $createSQL = "CREATE TABLE " . $tblInfo['tblName'] . "($fieldString)";         //Creating CREATE statement for the given entity

                $createResult = $this->prepExecClose($createSQL);                              //Calling helper method for Preparation, Execution, and Closing of CREATE statement

                $resultArray[$tblInfo['tblName']]['create'] = $createResult ?                  //Adding relevant resultArray message if creation successful/unsuccessful
                    $tblInfo['tblName'] . " was successfully created" :
                    "Error: " . $tblInfo['tblName'] . " failed to create. Execution error";
            }
        }
        return $resultArray;        //Returning resultArray
    }

    /***
     * Helper method to fill an array with the resulting rows from a query
     * @param $result   - Result object
     * @param $tblName  - Entity/Table name
     * @return array    - Array of results
     */
    private function parseRows($result, $tblName)
    {
        $resultSet = [];

        //Loop through the result to get the rows, and put rows into a 2D array
        while($tableRow = $result->fetchArray(SQLITE3_ASSOC))
        {
            $newEntity = new $tblName;       //Creates new entity instance

            foreach($tableRow as $field=>$val)          //Loops through tableRow properties and assigns them to the new entity
            {
                $newEntity->$field=$val;
            }
            $resultSet[] = $newEntity;              //Adds newEntity to result set
        }

        return $resultSet;
    }

    /***
     * Helper method to filter out empty fields and parse a whereString
     * If no properties are set, defaults to 1=1 to specify all entries
     * @param $tblInfo
     * @return string
     */
    private function filterFields($tblInfo)
    {
        $whereString = "";

        $filter = [];                                       //Array which will contain only properties with values assigned

        foreach($tblInfo['properties'] as $field=>$val)     //Loops through each key in $tblInfo['properties']. If its corresponding value is empty, it won't be added
        {
            if(!empty($val))
            {
                $filter[$field] = $val;                     //Adds an entry into Filter with KV of Field/Value
                $and = count($filter) > 1 ? 'AND' : "";     //Checks if there are multiple properties in Filter. If there are, will add an AND
                $whereString .= "$and$field=?";             //Concatenates field and "and" string into the where string
            }
            else
            {
                unset($tblInfo['properties'][$field]);      //If the field has no value assigned, unset it from properties
            }
        }
        return $whereString = empty($filter) ? " 1=1 " : $whereString;     //Checks if filter array is empty. Sets where condition to 1=1 to grab all entries of the entity type if field is empty
                                                                            //Uses WhereString as conditions if $filter is not empty
    }

    /***
     * Helper method to prepare, execute and close SQL statements
     * Returns -1 on preparation error, 0 on execution failure, and 1 on execution success
     * @param $sql - $sql string to prepare and execute
     * @return int - status code to indicate result of preparation or execution
     */
    private function prepExecClose($sql)
    {
        $stmt = $this->prepare($sql);           //Preparing statement
        if(!$stmt)                              //Check if statement preparation failed
        {
            return -1;                          //Returning -1 as status code to indicate preparation failure
        }
        $result = $stmt->execute();             //Executing statement, returning result to $result
        $stmt->close();                         //Closing statement

        return $result ? 1 : 0;                 //Returning 1 for success, 0 for failure
    }

    /***
     * Helper method to bind values to a statement, and execute the statement.
     * returns a result based on the success or failure of statements
     * @param $sql  - SQL statement string
     * @param $stmt - statement to be bound to and executed
     * @param $tblInfo  - information relating to the entity being added
     * @return int | \SQLite3Result - Status code indicating success/failure of statement
     */
    private function bindExec($stmt, $sql, $tblInfo)
    {
        if(!$stmt) { return -1; }   //Returning -1 as a status code to indicate the prepared statement failed

        $i = 1;
        foreach($tblInfo['properties'] as $field=>$val)
        {
//            $type = $tblInfo['propTypes'][$field];

            $type = $this->enumDataType($val);

            $sql .= " [$i: $field = $val, $type] ";
            $stmt->bindValue($i++, $val, $type);   //DONE: Figure out the bind type for the value
        }

        $this->lastStatement = $sql;    //For debugging

        //Execute the statement
        $result = $stmt->execute();

        return $result;
    }

    /***
     * Takes in an entity object and gets the primary key name, table name, and properties. Assigns them into an array
     * and returns the resulting array
     * @param $entity - Entity to be parsed
     * @return array - Array containing pkName, tblName, properties of entity
     */
    private function getTableInformation($entity)
    {
        $tblInfo = [];

        $tblInfo['pkName'] = $entity->getPkName();
        $tblInfo['tblName'] = $entity->getClassName();
        $tblInfo['properties'] = get_object_vars($entity);
        $tblInfo['propTypes'] = $entity->getPropTypes();

        return $tblInfo;
    }

    /***
     * SQLITE3 Data Types are silly and enumerate from multiple things to the same data types.
     * https://www.sqlite.org/datatype3.html
     * @param $prop - Value to get result type from
     * @return int - Resulting SQLITE3 affinity enumeration
     */
    private function enumDataType($prop)
    {
        switch(gettype($prop))
        {
            case("string"):
                $type = SQLITE3_TEXT;
                break;

            case("integer"):
                $type = SQLITE3_INTEGER;
                break;
            case("boolean"):
                $type = SQLITE3_NUM;
                break;
            case("double"):
                $type = SQLITE3_FLOAT;
                break;
            default:
                $type = SQLITE3_BLOB;
                break;
        }

        return $type;
    }

}