<?php

use Phinx\Migration\AbstractMigration;

class AddSessionSurveyTable extends AbstractMigration
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
        $table = $this->table('session_survey',['primary'=>'session_survey_id']);
        $table->addColumn('session_id','integer',['limit'=>10,'signed'=>false])
            ->addColumn('survey_id','integer')
            ->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('survey_id','survey','survey_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();

    }
}
