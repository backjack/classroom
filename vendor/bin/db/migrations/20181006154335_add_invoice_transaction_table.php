<?php

use Phinx\Migration\AbstractMigration;

class AddInvoiceTransactionTable extends AbstractMigration
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
        $table = $this->table('invoice_transaction',['id'=>'invoice_transaction_id']);
        $table->addColumn('invoice_id','integer')
            ->addColumn('date','integer')
            ->addColumn('status','string',['null'=>true])
            ->addColumn('amount','float')
            ->addForeignKey('invoice_id','invoice','invoice_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
