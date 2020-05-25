<?php

use Phinx\Migration\AbstractMigration;

class AddCouponCategoryTable extends AbstractMigration
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
        $table = $this->table('coupon_category',['id'=>'coupon_category_id']);
        $table->addColumn('coupon_id','integer')
            ->addColumn('session_category_id','integer')
            ->addForeignKey('coupon_id','coupon','coupon_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('session_category_id','session_category','session_category_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
