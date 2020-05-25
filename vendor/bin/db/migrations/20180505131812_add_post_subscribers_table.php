<?php

use Phinx\Migration\AbstractMigration;

class AddPostSubscribersTable extends AbstractMigration
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
        $table = $this->table('forum_post');
        $table->renameColumn('forum_post','forum_post_id')
        ->update();

        $table = $this->table('forum_participant',['id'=>'forum_participant_id']);
        $table->addColumn('forum_topic_id','integer')
            ->addColumn('user_id','integer')
            ->addColumn('user_type','char',['length'=>1])
            ->addColumn('notify','boolean')
             ->addForeignKey('forum_topic_id','forum_topic','forum_topic_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
             ->create();
    }
}
