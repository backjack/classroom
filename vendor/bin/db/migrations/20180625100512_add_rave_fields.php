<?php

use Phinx\Migration\AbstractMigration;

class AddRaveFields extends AbstractMigration
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
                $this->insert('payment_method_field',[
                   
                       [
                           'payment_method_id'=>10,
                           'key'=>'pkey',
                           'label'=>'Public Key',
                           'type'=>'text',
                       ],
                       [
                           'payment_method_id'=>10,
                           'key'=>'skey',
                           'label'=>'Secret Key',
                           'type'=>'text',
                       ],
                       [
                           'payment_method_id'=>10,
                           'key'=>'mode',
                           'label'=>'Payment Mode',
                           'type'=>'radio',
                           'options'=>'1=Live,0=Test'
                       ],
                   
                ]);
    }
}
