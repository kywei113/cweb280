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

    public function insert($entity) {
        if(count($entity->validate())){return -2;}

        $tableName = $entity->getClassName();
        $autoFields = $entity->getAutoIncrementColumnDefinitions();
        $filteredProperties = array_diff_key(get_object_vars($entity), $autoFields );

        $fieldString = implode(',', array_keys($filteredProperties));
        $qMarkString = str_repeat('?,', count($filteredProperties)-1) . '?';

        $this->lastStatement = "INSERT INTO $tableName ($fieldString) VALUES($qMarkString)";
        $stmt=$this->prepare($this->lastStatement);
        if(!$stmt){return -1;}

        $i = 1;
        foreach($filteredProperties as $field=>$val){
            $this->lastStatement .= " [$i:$field=$val] ";
            $stmt->bindValue($i++,$val,$entity->getBindType($field));
        }


        $result = $stmt->execute();
        if($result){
            $autoID = $this->lastInsertRowID();
            foreach (array_keys($autoFields) as $field){
                $entity->$field = $autoID;
            }
        }
        $stmt->close();
        return $result ? 1: 0;

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


    public function update($entity)
    {
        if(count($entity->validate())){return -2;}
       
        $properties = get_object_vars($entity);
        $pkName = $entity->getPkName();

        unset($properties[$pkName]);
        $setString = implode('=?, ',array_keys($properties)).'=?';

        $this->lastStatement = "UPDATE {$entity->getClassName()} SET $setString WHERE $pkName=?";
        $stmt=$this->prepare($this->lastStatement);
        if(!$stmt){return -1;}

        $i = 1;
        foreach($properties as $field=>$val){
            $this->lastStatement .= " [$i:$field=$val] ";
            $stmt->bindValue($i++,$val,$entity->getBindType($field));
        }

        $this->lastStatement .= " [$i:$pkName={$entity->$pkName}] ";
        $stmt->bindValue($i,$entity->$pkName,$entity->getBindType($pkName));


        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1: 0;

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