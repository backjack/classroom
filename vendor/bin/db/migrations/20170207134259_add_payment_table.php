<?php

use Phinx\Migration\AbstractMigration;

class AddPaymentTable extends AbstractMigration
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
        $table = $this->table('payment',['id'=>'payment_id']);
        $table->addColumn('amount','float')
                ->addColumn('student_id','integer',['limit'=>10,'signed'=>false])
                ->addColumn('added_on','integer')
                ->addColumn('payment_method_id','integer',['limit'=>10,'signed'=>false])
            ->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('payment_method_id','payment_method','payment_method_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
        ->save();

    }
}
