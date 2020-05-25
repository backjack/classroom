<?php

use Phinx\Migration\AbstractMigration;

class AddSessionLogTable extends AbstractMigration
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
            $table = $this->table('student_session_log',['primary'=>'student_session_log_id']);
            $table->addColumn('student_id','integer',['limit'=>10,'signed'=>false])
                ->addColumn('session_id','integer',['limit'=>10,'signed'=>false])
                ->addColumn('lesson_id','integer',['limit'=>10,'signed'=>false])
                ->addColumn('lecture_id','integer')
                ->addColumn('log_date','integer')
                ->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                ->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                 ->addForeignKey('lecture_id','lecture','lecture_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                ->addForeignKey('lesson_id','lesson','lesson_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                ->create();
    }
}
