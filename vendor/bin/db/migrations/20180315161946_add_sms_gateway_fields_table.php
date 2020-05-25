<?php

use Phinx\Migration\AbstractMigration;

class AddSmsGatewayFieldsTable extends AbstractMigration
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
        $table = $this->table('sms_gateway_field',['id'=>'sms_gateway_field_id']);
        $table->addColumn('sms_gateway_id','integer')
            ->addForeignKey('sms_gateway_id','sms_gateway','sms_gateway_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addColumn('label','string')
            ->addColumn('key','string')
            ->addColumn('type','string')
            ->addColumn('value','text',['null'=>true])
            ->addColumn('options','text',['null'=>true])
            ->addColumn('class','string',['null'=>true])
            ->addColumn('placeholder','string',['null'=>true])
            ->addColumn('sort_order','integer',['null'=>true])
            ->create();
    }
}
