<?php

use Phinx\Migration\AbstractMigration;

class AddSurveyResponseOptionTable extends AbstractMigration
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
        $table = $this->table('survey_response_option',['id'=>'survey_response_option_id'])
            ->addColumn('survey_response_id','integer')
            ->addForeignKey('survey_response_id','survey_response','survey_response_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addColumn('survey_option_id','integer')
            ->addForeignKey('survey_option_id','survey_option','survey_option_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();

    }
}
