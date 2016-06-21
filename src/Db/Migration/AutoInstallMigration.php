<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 21/06/16
 * Time: 09:58
 */

namespace efrogg\Db\Migration;


class AutoInstallMigration extends Migration
{

    /**
     * @var
     */
    private $tableName = "migrations";


    /**
     * InstallMigration constructor.
     * @param string $name
     */
    public function __construct($tableName=null)
    {
        if(!is_null($tableName)) {
            $this->tableName = $tableName;
        }
    }


    public function up()
    {
        $sql = "CREATE TABLE ".$this->tableName." (
            `id_migration` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration_name` VARCHAR(512) NOT NULL ,
            `batch` SMALLINT UNSIGNED NOT NULL ,
            INDEX `batch` (`batch`),
            PRIMARY KEY `id_migration` (`id_migration`),
            UNIQUE `migration_name` (`migration_name`)
          )
          ENGINE = InnoDB;";
        $executed = $this->db->execute($sql);
//        if(!$executed->isValid()) {
//            var_dump($executed->getErrorMessage());
//        }
    }

    public function down()
    {
        // pas de drop sur celui-la :)
//        $this->db->execute("DROP TABLE IF EXISTS ".$this->tableName);
    }

    public function isFixed()
    {
        return true;
    }
}