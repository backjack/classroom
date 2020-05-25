<?php

use Phinx\Migration\AbstractMigration;

class RenamePaypal extends AbstractMigration
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
        $this->execute("UPDATE payment_method SET payment_method='Paypal' where payment_method_id=2");
        $this->execute("DELETE FROM payment_method WHERE payment_method_id=5");
        $this->execute("UPDATE payment_method SET payment_method_id=5 WHERE payment_method_id=6");

    }
}
