<?php

namespace efrogg\Db\Adapters;


interface DbAdapter {
    /**
     * @param $query
     * @param array $params
     * @param bool $forceMaster
     * @return DbResultAdapter
     */
    public function execute($query,$params=array(), $forceMaster=false);

    /**
     * @return string
     */
    public function getError();

    /**
     * @return int
     */
    public function getInsertId();

    /**
     * @return int
     */
    public function getAffectedRows();


    public function throwsExceptions();
}