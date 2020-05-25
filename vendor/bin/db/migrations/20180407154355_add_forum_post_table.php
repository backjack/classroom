<?php

use Phinx\Migration\AbstractMigration;

class AddForumPostTable extends AbstractMigration
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
        $table = $this->table('forum_post',['id'=>'forum_post']);
        $table->addColumn('forum_topic_id','integer');
        $table->addColumn('message','text');
        $table->addColumn('post_created_on','integer');
        $table->addColumn('post_owner','integer');
        $table->addColumn('post_owner_type','char',['length'=>1]);
        $table->addColumn('post_reply_id','integer',['null'=>true]);
        $table->addForeignKey('forum_topic_id','forum_topic','forum_topic_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
