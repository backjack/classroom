<?php

use Phinx\Migration\AbstractMigration;

class AddCurrencyToPaypalex extends AbstractMigration
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
        $this->execute("UPDATE payment_method set currency='AUD CAD EUR GBP JPY USD NZD CHF HKD SGD SEK DKK PLN NOK HUF CZK ILS MXN MYR BRL PHP TWD THB TRY' where payment_method_id='5'");

    }
}
