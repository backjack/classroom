<?php

use Phinx\Migration\AbstractMigration;

class AddBookmarkTable extends AbstractMigration
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
        $table = $this->table('lecture_page',['id'=>'lecture_page_id']);
        $table->addColumn('lecture_id','integer')
                ->addColumn('title','string')
                ->addColumn('content','text')
                ->addColumn('type','char')
                ->addColumn('sort_order','integer',['null'=>true])
                ->addForeignKey('lecture_id','lecture','lecture_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                ->create();

        $table = $this->table('bookmark',['id'=>'bookmark_id']);
        $table->addColumn('session_id','integer',['limit'=>10,'signed'=>false])
            ->addColumn('student_id','integer',['limit'=>10,'signed'=>false])
            ->addColumn('lecture_page_id','integer')
            ->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('lecture_page_id','lecture_page','lecture_page_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();






    }
}
