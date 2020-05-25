<?php

use Phinx\Migration\AbstractMigration;

class AddCurrencyTable extends AbstractMigration
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
        $table = $this->table('currency',['id'=>'currency_id']);
        $table->addColumn('country_id','integer')
            ->addColumn('exchange_rate','float',['signed'=>false])
            ->addForeignKey('country_id','country','country_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
