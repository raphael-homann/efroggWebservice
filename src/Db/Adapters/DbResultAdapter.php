<?php
namespace efrogg\Db\Adapters;

interface DbResultAdapter {
    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public function fetch();

    /**
     * @param null $class_name
     * @param array $params
     * @return array
     */
    public function fetchObject($class_name = null, array $params = null);

    /**
     * @return array[]
     */
    public function fetchAll();

    /**
     * @param $column_name
     * @return array
     */
    public function fetchColumn($column_name);

    public function fetchAllObject($class_name = null, array $params = null);

    public function getErrorCode();

    public function getErrorMessage();
}