<?php

use Phinx\Migration\AbstractMigration;

class AddStudentVideoTable extends AbstractMigration
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
        $table = $this->table('student_video',['id'=>'student_video_id']);
        $table->addColumn('student_id','integer',['limit'=>10,'signed'=>false])
            ->addColumn('video_id','integer')
            ->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('video_id','video','video_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->save();
    }
}
