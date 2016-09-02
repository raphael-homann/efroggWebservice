<?php
namespace efrogg\Webservice;

use efrogg\Db\Adapters\DbAdapter;

abstract class Webservice implements WebserviceInterface
{

    /** @var  DbAdapter */
    protected $db;

    /**
     * Webservice constructor.
     * @param DbAdapter $db
     */
    public function __construct(DbAdapter $db)
    {
        $this->setDb($db);
    }

    public function setDb(DbAdapter $db) {
        $this->db = $db;
    }


}