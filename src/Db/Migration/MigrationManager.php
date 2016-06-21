<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 21/06/16
 * Time: 09:58
 */

namespace efrogg\Db\Migration;

use efrogg\Db\Adapters\DbAdapter;

class MigrationManager
{
    /** @var  DbAdapter */
    protected $db;

    /** @var Migration[]  */
    protected $migrations = array();

    /** @var int  */
    protected $batchNumber = 1;

    /** @var int  */
    protected $batchCount = 0;

    /**
     * @var string
     */
    protected $migrationTableName = "migrations";

    /** @var string */
    protected $last_error;

    /**
     * MigrationManager constructor.
     * @param DbAdapter $db
     */
    public function __construct(DbAdapter $db)
    {
        $this->db = $db;
        $db->throwsExceptions();
        $this->addMigration(new AutoInstallMigration($this->migrationTableName));               // auto install ;)
    }

    public function addMigration(Migration $migration) {
        $this->migrations[]=$migration;
        $migration->setDb($this->db);
    }



    /**
     * @return int :
     *  0 = aucune migration jouée
     *  -1 = erreur
     *  n = nb de migrations jouées
     */
    public function up() {
        $this->batchNumber = $this->getLastBatchNumber()+1;
        foreach($this->migrations as $migration) {
            if(!$this->migrationExists($migration)) {
                try {
                    $this->db->execute("START TRANSACTION");
                    $migration->up();
                    $this->db->execute("COMMIT");
                    $this->saveMigration($migration,$this->batchNumber);
                    $this->batchCount++;
                } catch(\Exception $e) {
                    // stocke le message d'erreur
                    $this->last_error = $e->getMessage();
                    // et stoppe les migrations
                    $this->db->execute("ROLLBACK");

                    return false;
                }
            }
        }

        return true;
    }

    public function down() {
        if($this->isInstalled()) {
            $migrationList = $this->db
                ->execute("SELECT migration_name FROM ".$this->migrationTableName." WHERE batch=".$this->getLastBatchNumber()." ORDER BY id_migration DESC")
                ->fetchColumn("migration_name");
            foreach($migrationList as $className) {
                /** @var Migration $migration */
                $migration = new $className();
                if($migration->isFixed()) continue;
                $migration->setDb($this->db);
                try {
                    $migration->down();
                    $this->batchCount++;
                    $this->removeMigration($migration);
                } catch(\Exception $e) {
                    $this->last_error = $e->getMessage();
                    return false;
                }
            }
        }
        return true;
    }

    public function getBatchNumber() {
        return $this->batchNumber;
    }


    private $migrationList = null;
    private function getExistingMigrations()
    {
        if(is_null($this->migrationList)) {
            if($this->isInstalled()) {
                $this->migrationList = $this->db->execute("SELECT migration_name FROM ".$this->migrationTableName)->fetchColumn("migration_name");
            } else {
                $this->migrationList=array();
            }
        }
        return $this->migrationList;
    }

    private function isInstalled()
    {
        return (count($this->db->execute("SHOW TABLES LIKE '".$this->migrationTableName."'")->fetchAll())>0);
    }

    private function getLastBatchNumber()
    {
        if(!$this->isInstalled()) {
            return 0;
        }
        $data = $this->db->execute("SELECT MAX(batch) AS maxi FROM ".$this->migrationTableName)->fetch();
        return $data["maxi"];
    }


    private function migrationExists(Migration $migration)
    {
        return array_search($migration->getName(),$this->getExistingMigrations())!==false;
    }

    private function saveMigration(Migration $migration, $batchNumber)
    {
        $query = $this->db->execute("INSERT INTO ".$this->migrationTableName." (migration_name,batch) VALUES (?,?)",array($migration->getName(),$batchNumber));
        if(!$query->isValid()) {
            var_dump($query->getErrorMessage());
        }
        $this->migrationList[]=$migration->getName();               // maintient de l'indexs
    }
    private function removeMigration(Migration $migration)
    {
        $sql = "DELETE FROM ".$this->migrationTableName." WHERE migration_name = ?";
        $query = $this->db->execute($sql,array($migration->getName()));

        if(!$query->isValid()) {
            var_dump($query->getErrorMessage());
        }
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * @return int
     */
    public function getBatchCount()
    {
        return $this->batchCount;
    }

}