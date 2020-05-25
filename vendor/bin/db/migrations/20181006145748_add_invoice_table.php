<?php

use Phinx\Migration\AbstractMigration;

class AddInvoiceTable extends AbstractMigration
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
        $table = $this->table('invoice',['id'=>'invoice_id']);
        $table->addColumn('student_id','integer',['signed'=>false])
            ->addColumn('currency_id','integer',['signed'=>true])
            ->addColumn('created_on','integer')
            ->addColumn('amount','float')
            ->addColumn('cart','text')
            ->addColumn('paid','boolean',['default'=>0])
            ->addForeignKey('currency_id','currency','currency_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();

    }
}
