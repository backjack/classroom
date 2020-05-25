<?php

use Phinx\Migration\AbstractMigration;

class AddPayFast extends AbstractMigration
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
            'payment_method_id'=>7,
            'payment_method'=>'Payfast.co.za',
            'method_label'=>'Payfast: Online Payment',
            'status'=>0,
            'code'=>'payfast',
            'sort_order'=>0,
            'currency'=>"ZAR"
        ]);

        $this->insert('payment_method_field',[
            [
                'payment_method_id'=>7,
                'key'=>'payfast_merchant_id',
                'label'=>'PayFast Merchant ID',
                'placeholder'=>'',
                'type'=>'text'
            ],
            [
                'payment_method_id'=>7,
                'key'=>'payfast_merchant_key',
                'label'=>'PayFast Merchant Key',
                'placeholder'=>'',
                'type'=>'text'
            ],
            [
                'payment_method_id'=>7,
                'key'=>'payfast_sandbox',
                'label'=>'Sandbox Mode',
                'placeholder'=>'',
                'type'=>'select',
                'options'=>'1=Yes,0=No'
            ],
            [
                'payment_method_id'=>7,
                'key'=>'payfast_debug',
                'label'=>'Debug',
                'placeholder'=>'',
                'type'=>'select',
                'options'=>'1=Yes,0=No'
            ],
            [
                'payment_method_id'=>7,
                'key'=>'payfast_passphrase',
                'label'=>'PayFast Secure Passphrase',
                'placeholder'=>'',
                'type'=>'text',
            ],


        ]);
        
    }
}
