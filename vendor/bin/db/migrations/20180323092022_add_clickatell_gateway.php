<?php

use Phinx\Migration\AbstractMigration;

class AddClickatellGateway extends AbstractMigration
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
        $this->insert('sms_gateway',[
            'sms_gateway_id'=>3,
            'gateway_name'=>'Clickatell',
            'url'=>'https://www.clickatell.com',
            'country'=>'South Africa, United States',
            'code'=>'clickatell',
            'about'=>'SA born Clickatell offers global sms solutions'
        ]);

        $this->insert('sms_gateway_field',[
           'sms_gateway_id' =>3,
            'label'=>'Api Key',
            'key'=>'apikey',
            'type'=>'text',
            'sort_order'=>1
        ]);
    }
}
