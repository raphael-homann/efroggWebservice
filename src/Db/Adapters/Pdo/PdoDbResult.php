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
        return $this -> statement -> fetchColumn($column);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this -> statement !== false;
    }

    /**
     * @param $class_name
     * @param array $params
     * @return array
     */
    public function fetchObject($class_name = "stdClass", array $params = null)
    {
        return $this -> statement -> fetchObject($class_name,$params);
    }

    public function fetchAllObject($class_name = "stdClass", array $params = null)
    {
        $result=array();
        while($line = $this->fetchObject($class_name,$params)) {
            $result[]=$line;
        }
        return $result;
    }
}