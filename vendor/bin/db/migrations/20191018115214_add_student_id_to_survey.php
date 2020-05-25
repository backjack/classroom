<?php

use Phinx\Migration\AbstractMigration;

class AddStudentIdToSurvey extends AbstractMigration
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
        $table = $this->table('survey_response');
        $table->addColumn('student_id','integer',['limit'=>10,'signed'=>false,'null'=>true])
            ->addForeignKey('student_id','student','student_id',['delete'=>'SET NULL','update'=>'NO_ACTION'])
            ->update();
    }
}
