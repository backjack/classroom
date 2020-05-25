<?php

use Phinx\Migration\AbstractMigration;

class AddPayuMoneyGateway extends AbstractMigration
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
            'payment_method_id'=>8,
            'payment_method'=>'PayU money',
            'method_label'=>'PayU money: Online Payment',
            'status'=>0,
            'code'=>'payumoney',
            'sort_order'=>0,
            'currency'=>"INR"
        ]);


        $this->insert('payment_method_field',[
            [
                'payment_method_id'=>8,
                'key'=>'payumoney_merchant_key',
                'label'=>'Merchant Key',
                'placeholder'=>'',
                'type'=>'text'
            ],
            [
                'payment_method_id'=>8,
                'key'=>'payumoney_salt',
                'label'=>'Salt',
                'placeholder'=>'',
                'type'=>'text'
            ],
            [
                'payment_method_id'=>8,
                'key'=>'payumoney_sandbox',
                'label'=>'Sandbox Mode',
                'type'=>'select',
                'options'=>'1=Yes,0=No'
            ],



        ]);

    }
}
