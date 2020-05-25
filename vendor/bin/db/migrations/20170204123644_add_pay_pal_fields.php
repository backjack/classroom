<?php

use Phinx\Migration\AbstractMigration;

class AddPayPalFields extends AbstractMigration
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
        $data= [
            [
                'key'=>'clientid',
                'label'=>'Paypal Client Id',
                'type'=>'text',
                'payment_method_id'=>'2'
            ],
            [
                'key'=>'secret',
                'label'=>'Paypal Secret',
                'type'=>'text',
                'payment_method_id'=>2
            ],
            [
                'key'=>'mode',
                'label'=>'Mode',
                'type'=>'radio',
                'options'=>'1=Live,0=Test',
                'payment_method_id'=>2
            ]
        ];

        $this->insert('payment_method_field',$data);
    }
}
