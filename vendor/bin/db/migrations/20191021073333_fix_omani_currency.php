<?php

use Phinx\Migration\AbstractMigration;

class FixOmaniCurrency extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $dbParams= include '../../config/autoload/local.php';
        $dbName = $dbParams['db']['dbname'];


        $this->execute("ALTER DATABASE {$dbName} CHARACTER SET utf8 COLLATE utf8_general_ci");
        $this->execute("ALTER TABLE country CONVERT TO CHARACTER SET utf8");
        $this->execute("ALTER TABLE country MODIFY symbol_left VARCHAR(45) CHARACTER SET utf8");

        $this->execute("update country set symbol_left='ر.ع' where country_id=161");
    }
}
