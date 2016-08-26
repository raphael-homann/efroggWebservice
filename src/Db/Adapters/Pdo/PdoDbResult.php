<?php
namespace efrogg\Db\Adapters\Pdo;


use efrogg\Db\Adapters\DbResultAdapter;

class PdoDbResult implements DbResultAdapter {

    /**
     * @var resource
     */
    private $statement = false;

    public function __construct(\PDOStatement $statement) {
        $this->statement = $statement;
    }

    public function fetch()
    {
        return $this -> statement -> fetch();
    }

    public function fetchAll()
    {
        return $this -> statement -> fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchColumn($column = 0)
    {
        if(is_int($column)) {
            return $this -> statement -> fetchColumn($column);
        } else {
            $column_data=array();
            foreach($this->fetchAll() as $row) {
                $column_data[]=$row[$column];
            }
            return $column_data;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return intval($this -> statement->errorCode())==0;
    }

    /**
     * @param $class_name
     * @param array $params
     * @return array
     */
    public function fetchObject($class_name = "stdClass", array $params = null)
    {
        if(is_null($params)) {
            return $this -> statement -> fetchObject($class_name);
        } else {
            return $this -> statement -> fetchObject($class_name,$params);
        }
    }

    public function fetchAllObject($class_name = "stdClass", array $params = null)
    {
        $result=array();
        while($line = $this->fetchObject($class_name,$params)) {
            $result[]=$line;
        }
        return $result;
    }

    public function getErrorCode()
    {
        $info = $this -> statement->errorInfo();
        return $info[1];
//        return $this -> statement->errorCode();
    }

    public function getErrorMessage()
    {
        $info = $this -> statement->errorInfo();
        return $info[2];
    }
}