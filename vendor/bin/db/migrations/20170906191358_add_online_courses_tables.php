<?php

use Phinx\Migration\AbstractMigration;

class AddOnlineCoursesTables extends AbstractMigration
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
        $table = $this->table('session');
        $table->changeColumn('enrollment_closes','integer',['null'=>true]);
        $table->addColumn('session_type','char',['default'=>'s']);
        $table->addColumn('picture','string',['limit'=>250]);
        $table->update();

        $table = $this->table('lesson');
        $table->addColumn('test_required','boolean',['default'=>0]);
        $table->addColumn('test_id','integer',['limit'=>10,'signed'=>false,'null'=>true]);
        $table->update();

        $table = $this->table('discussion');
        $table->addColumn('session_id','integer',['limit'=>10,'signed'=>false,'null'=>true]);
        $table->addColumn('lecture_id','integer',['limit'=>10,'signed'=>false,'null'=>true]);
        $table->update();

        $table = $this->table('session_category',['id'=>'session_category_id']);
        $table->addColumn('category_name','string',['limit'=>250]);
        $table->addColumn('status','boolean',['default'=>1]);
        $table->addColumn('sort_order','integer',['null'=>true]);
        $table->create();

        $table= $this->table('session_to_session_category',['id'=>'session_to_session_category_id']);
        $table->addColumn('session_category_id','integer');
        $table->addColumn('session_id','integer',['limit'=>10,'signed'=>false]);
        $table->addForeignKey('session_category_id','session_category','session_category_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->create();

        $table = $this->table('lesson_group',['id'=>'lesson_group_id']);
        $table->addColumn('group_name','string',['limit'=>250]);
        $table->addColumn('description','text',['null'=>true]);
        $table->create();

        $table = $this->table('lesson_to_lesson_group',['id'=>'lesson_to_lesson_group_id']);
        $table->addColumn('lesson_group_id','integer');
        $table->addColumn('lesson_id','integer',['limit'=>10,'signed'=>false]);
        $table->addForeignKey('lesson_group_id','lesson_group','lesson_group_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->addForeignKey('lesson_id','lesson','lesson_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->create();

        $table = $this->table('lecture',['id'=>'lecture_id']);
        $table->addColumn('lesson_id','integer',['limit'=>10,'signed'=>false]);
        $table->addColumn('lecture_title','string',['limit'=>250]);
        $table->addColumn('content','text',['null'=>true]);
        $table->addColumn('video_code','text',['null'=>true]);
        $table->addColumn('sort_order','integer',['limit'=>10,'null'=>true]);
        $table->addForeignKey('lesson_id','lesson','lesson_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->create();

        $table = $this->table('student_lecture',['id'=>'student_lecture_id']);
        $table->addColumn('student_id','integer',['limit'=>10,'signed'=>false]);
        $table->addColumn('session_id','integer',['limit'=>10,'signed'=>false]);
        $table->addColumn('lecture_id','integer');
        $table->addColumn('date','integer',['limit'=>10]);
        $table->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->addForeignKey('lecture_id','lecture','lecture_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->create();

        $table = $this->table('lesson_file',['id'=>'lesson_file_id']);
        $table->addColumn('lesson_id','integer',['limit'=>10,'signed'=>false]);
        $table->addColumn('path','text');
        $table->addColumn('created_on','integer',['limit'=>10]);
        $table->addColumn('status','boolean',['default'=>1]);
        $table->addForeignKey('lesson_id','lesson','lesson_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->create();

        $table = $this->table('lecture_file',['id'=>'lecture_file_id']);
        $table->addColumn('lecture_id','integer');
        $table->addColumn('path','text');
        $table->addColumn('created_on','integer',['limit'=>10]);
        $table->addColumn('status','boolean',['default'=>1]);
        $table->addForeignKey('lecture_id','lecture','lecture_id',['delete'=>'CASCADE','update'=>'NO_ACTION']);
        $table->create();


    }
}
