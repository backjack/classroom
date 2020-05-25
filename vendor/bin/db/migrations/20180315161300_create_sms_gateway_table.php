<?php

use Phinx\Migration\AbstractMigration;

class CreateSmsGatewayTable extends AbstractMigration
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
        $table = $this->table('sms_gateway',['id'=>'sms_gateway_id']);
        $table->addColumn('gateway_name','string')
            ->addColumn('url','string')
            ->addColumn('country','string')
            ->addColumn('code','string')
            ->addColumn('active','integer',['default'=>0])
            ->addColumn('about','text',['null'=>true])
            ->create();
    }
}
