<?php

use Phinx\Migration\AbstractMigration;

class AddGhanianPaymentGateway extends AbstractMigration
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
                'payment_method_id'=>9,
                'payment_method'=>'iPay',
                'method_label'=>'iPay: Online Payment',
                'status'=>0,
                'code'=>'ipay',
                'sort_order'=>0,
                'currency'=>"GHS"
            ]);


            $this->insert('payment_method_field',[
                [
                    'payment_method_id'=>9,
                    'key'=>'ipay_merchant_key',
                    'label'=>'Merchant Key',
                    'placeholder'=>'',
                    'type'=>'text'
                ]

            ]);


        }
}
