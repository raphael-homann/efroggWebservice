<?php

namespace efrogg\Db\Adapters\Mysql;

use efrogg\Db\Adapters\DbAdapter;
use efrogg\Db\Adapters\DbResultAdapter;
use efrogg\Db\Adapters\Mysql\MysqlDbResult;
use efrogg\Db\Adapters\Pdo\PdoDbResult;

class MysqlDbAdapter implements DbAdapter{
    /** @var  resource */
    protected $db;


    /**
     * PrestashopDbAdapter constructor.
     */
    public function __construct($db)
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
        return new MysqlDbResult(mysql_query($query,$this->db));
    }

    /**
     * @return string
     */
    public function getError()
    {
        return mysql_error($this->db);
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        return mysql_insert_id($this->db);
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        return mysql_affected_rows($this->db);
    }
}