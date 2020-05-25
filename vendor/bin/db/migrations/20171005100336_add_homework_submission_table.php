<?php

use Phinx\Migration\AbstractMigration;

class AddHomeworkSubmissionTable extends AbstractMigration
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
        $table = $this->table('assignment_submission',['id'=>'assignment_submission_id']);
        $table->addColumn('assignment_id','integer')
            ->addColumn('student_id','integer',['limit'=>10,'signed'=>false])
            ->addColumn('content','text',['null'=>true])
            ->addColumn('file_path','text',['null'=>'true'])
            ->addColumn('grade','float',['null'=>true])
            ->addColumn('editable','boolean',['null'=>true,'default'=>0])
            ->addForeignKey('assignment_id','assignment','assignment_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
