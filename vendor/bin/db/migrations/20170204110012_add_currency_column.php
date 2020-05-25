<?php

use Phinx\Migration\AbstractMigration;

class AddCurrencyColumn extends AbstractMigration
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
        $table = $this->table('payment_method');
        $table->addColumn('currency','text');
        $table->save();

        $this->execute("UPDATE payment_method set currency='ANY' where payment_method_id='1'");
        $this->execute("UPDATE payment_method set currency='AUD CAD EUR GBP JPY USD NZD CHF HKD SGD SEK DKK PLN NOK HUF CZK ILS MXN MYR BRL PHP TWD THB TRY' where payment_method_id='2'");
        $this->execute("UPDATE payment_method set currency='AUD CAD EUR GBP JPY USD NZD CHF HKD SGD SEK DKK PLN NOK HUF CZK ILS MXN MYR BRL PHP TWD THB TRY' where payment_method_id='3'");
        $this->execute("UPDATE payment_method set currency='NGN' where payment_method_id='4'");

    }
}
