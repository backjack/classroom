<?php

use Phinx\Migration\AbstractMigration;

class AddVideoTable extends AbstractMigration
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
        $table = $this->table('video',['id'=>'video_id']);
        $table->addColumn('name','string')
            ->addColumn('description','text',['null'=>true])
            ->addColumn('created_at','integer')
            ->addColumn('account_id','integer',['limit'=>10,'signed'=>false])
            ->addColumn('ready','boolean')
            ->addColumn('length','string')
            ->addForeignKey('account_id','accounts','account_id',['delete'=>'RESTRICT','update'=>'NO_ACTION'])
            ->create();


    }
}
