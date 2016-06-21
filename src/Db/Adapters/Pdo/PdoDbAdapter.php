<?php

namespace efrogg\Db\Adapters\Pdo;

use efrogg\Db\Adapters\DbAdapter;
use efrogg\Db\Adapters\DbResultAdapter;
use efrogg\Db\Exception\DbException;
use efrogg\Db\Adapters\Mysql\MysqlDbResult;
use efrogg\Db\Adapters\Pdo\PdoDbResult;

class PdoDbAdapter implements DbAdapter{
    /** @var  \PDO */
    protected $db;
    protected $throws_exceptions = false;


    /**
     * PrestashopDbAdapter constructor.
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }


    /**
     * @param $query
     * @param array $params
     * @param bool $forceMaster
     * @return DbResultAdapter
     */
    public function execute($query, $params = array(), $forceMaster = false)
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        $result = new PdoDbResult($stmt);
        if($this->throws_exceptions && !$result->isValid()) {
//            var_dump($result->getErrorMessage(),$result->getErrorCode());
            throw new DbException($result->getErrorMessage(),$result->getErrorCode());
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->db->errorInfo();
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        return 0; //TODO
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        return 0;//TODO
    }

    public function throwsExceptions($throws = true)
    {
        $this->throws_exceptions = $throws;
    }
}