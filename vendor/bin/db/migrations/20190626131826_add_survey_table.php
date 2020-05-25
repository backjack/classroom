<?php

use Phinx\Migration\AbstractMigration;

class AddSurveyTable extends AbstractMigration
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
        $table = $this->table('survey',['id'=>'survey_id']);
        $table->addColumn('name','string')
            ->addColumn('description','text')
            ->addColumn('status','boolean')
            ->addColumn('created_on','integer')
            ->addColumn('hash','string')
            ->create();
    }
}
