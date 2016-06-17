<?php

namespace efrogg\Db\Adapters\Pdo;

use efrogg\Db\Adapters\DbAdapter;
use efrogg\Db\Adapters\DbResultAdapter;
use efrogg\Db\Adapters\Mysql\MysqlDbResult;
use efrogg\Db\Adapters\Pdo\PdoDbResult;

class PdoDbAdapter implements DbAdapter{
    /** @var  \PDO */
    protected $db;


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
        return new PdoDbResult($stmt);
    }

    /**
     * @return string
     */
    public function getError()
    {
        return "TODO";
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
}