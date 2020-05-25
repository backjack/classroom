<?php

use Phinx\Migration\AbstractMigration;

class AddCouponInvoiceTable extends AbstractMigration
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
        $table = $this->table('coupon_invoice',['id'=>'coupon_invoice_id']);
        $table->addColumn('coupon_id','integer')
            ->addColumn('invoice_id','integer')
            ->addForeignKey('coupon_id','coupon','coupon_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('invoice_id','invoice','invoice_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
