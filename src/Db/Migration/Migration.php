<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 21/06/16
 * Time: 09:57
 */

namespace efrogg\Db\Migration;

use efrogg\Db\Adapters\DbAdapter;

abstract class Migration
{
    /** @var  DbAdapter */
    protected $db;


    abstract public function up();
    abstract public function down();

    /**
     * @param DbAdapter $db
     * @return Migration
     */
    public function setDb($db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }

    public function isFixed()
    {
        return false;
    }
}