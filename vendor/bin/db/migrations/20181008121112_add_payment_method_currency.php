<?php

use Phinx\Migration\AbstractMigration;

class AddPaymentMethodCurrency extends AbstractMigration
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
        $table = $this->table('payment_method_currency',['id'=>'payment_method_currency_id']);
        $table->addColumn('payment_method_id','integer',['signed'=>false])
            ->addColumn('currency_id','integer',['signed'=>true])
            ->addForeignKey('payment_method_id','payment_method','payment_method_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('currency_id','currency','currency_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
