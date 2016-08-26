<?php

namespace efrogg\Db\Adapters\Prestashop;

use efrogg\Db\Adapters\DbAdapter;
use efrogg\Db\Adapters\DbResultAdapter;
use efrogg\Db\Adapters\Mysql\MysqlDbResult;
use efrogg\Db\Adapters\Pdo\PdoDbResult;

class PrestashopDbAdapter implements DbAdapter{
    /** @var  \Db */
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
        if($this->db instanceof \MySQL) {
            return new MysqlDbResult($this->db -> query($query));
        } elseif($this->db instanceof \DbPDOCore) {
            return new PdoDbResult($this->db -> query($query));
        } else {
            throw new \Exception("datapase type unknown : ".get_class($this->db));
        }
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->db->getMsgError();
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        $this->db->Insert_ID();
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        $this->db->Affected_Rows();
    }

    public function throwsExceptions()
    {
        // TODO: Implement throwsExceptions() method.
    }
}