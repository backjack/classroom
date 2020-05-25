<?php

use Phinx\Migration\AbstractMigration;

class AddLessonAdminTable extends AbstractMigration
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
        $table = $this->table('session_lesson_account',['id'=>'session_lesson_account_id']);
        $table->addColumn('session_lesson_id','integer',['limit'=>10,'signed'=>false])
                ->addColumn('account_id','integer',['limit'=>10,'signed'=>false])
            ->addForeignKey('session_lesson_id','session_lesson','session_lesson_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('account_id','accounts','account_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                ->create();
    }
}
