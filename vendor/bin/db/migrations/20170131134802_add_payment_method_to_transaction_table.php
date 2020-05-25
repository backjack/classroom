<?php

use Phinx\Migration\AbstractMigration;

class AddPaymentMethodToTransactionTable extends AbstractMigration
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
        $total = $this->execute('DELETE FROM transaction');
        $total = $this->execute('ALTER TABLE `transaction` ADD COLUMN `payment_method_id` INTEGER UNSIGNED NOT NULL AFTER `session_id`,
 ADD CONSTRAINT `FK_transaction_2` FOREIGN KEY `FK_transaction_2` (`payment_method_id`)
    REFERENCES `payment_method` (`payment_method_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
');


    }
}
