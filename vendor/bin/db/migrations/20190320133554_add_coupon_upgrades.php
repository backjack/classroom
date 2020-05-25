<?php

use Phinx\Migration\AbstractMigration;

class AddCouponUpgrades extends AbstractMigration
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
        $table= $this->table('coupon');
        $table->addColumn('name','string')
            ->addColumn('type','char')
            ->addColumn('total','float',['null'=>true])
            ->addColumn('date_start','integer',['null'=>true])
            ->addColumn('uses_total','integer',['null'=>true])
            ->addColumn('uses_customer','integer',['null'=>true])
            ->update();

        //set default values
        $this->execute("update coupon set date_start=0");

        $rowset = $this->query("SELECT * FROM coupon");
        foreach($rowset as $row){
            $id= $row['coupon_id'];
            $name = $row['code'];
            $this->execute("update coupon set name='{$name}' where coupon_id={$id}");



        }
    }

}
