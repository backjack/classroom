<?php

use Phinx\Migration\AbstractMigration;

class AddQuestionTables extends AbstractMigration
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
        $table = $this->table('discussion',['id'=>'discussion_id']);
        $table->addColumn('student_id','integer',['limit'=>10,'signed'=>false])
                ->addColumn('subject','string',['limit'=>250])
                ->addColumn('question','text')
            ->addColumn('created_on','integer')
            ->addForeignKey('student_id','student','student_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->save();

        $table = $this->table('discussion_reply',['discussion_reply_id']);
        $table->addColumn('discussion_id','integer')
            ->addColumn('reply','text')
            ->addColumn('replied_on','integer')
            ->addColumn('user_id','integer')
            ->addColumn('user_type','string')
            ->addForeignKey('discussion_id','discussion','discussion_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->save();

    }
}
