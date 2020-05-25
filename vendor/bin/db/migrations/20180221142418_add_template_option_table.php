<?php

use Phinx\Migration\AbstractMigration;

class AddTemplateOptionTable extends AbstractMigration
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
        $table = $this->table('template_option',['id'=>'template_option_id']);
        $table->addColumn('template_id','integer')
            ->addForeignKey('template_id','template','template_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addColumn('label','string')
            ->addColumn('key','string')
            ->addColumn('type','string')
            ->addColumn('value','text',['null'=>true])
            ->addColumn('group','string')
            ->addColumn('options','text',['null'=>true])
            ->addColumn('class','string',['null'=>true])
            ->addColumn('placeholder','string',['null'=>true])
            ->addColumn('sort_order','integer',['null'=>true])
            ->create();

    }
}
