<?php

use Phinx\Migration\AbstractMigration;

class AddRaveGateway extends AbstractMigration
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
        $this->insert('payment_method',[
                'payment_method_id'=> '10',
                'payment_method'=>'Rave by Flutterwave',
                'status'=>'0',
                'sort_order'=>'0',
                'code'=>'rave',
                'currency'=>'NGN ZAR GHS KES USD GBP EUR',
                'method_label'=>'Online Payment (Rave)'            
        ]);
        


    }
}
