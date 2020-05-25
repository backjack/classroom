<?php

use Phinx\Migration\AbstractMigration;

class AddForumTables extends AbstractMigration
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
        $table = $this->table('forum_topic',['id'=>'forum_topic_id']);
        $table->addColumn('topic_title','string');
        $table->addColumn('created_on','integer');
        $table->addColumn('topic_owner','integer');
        $table->addColumn('topic_owner_type','char',['length'=>1]);
        $table->addColumn('session_id','integer',['limit'=>10,'signed'=>false]);
        $table->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                ->create();

    }
}
