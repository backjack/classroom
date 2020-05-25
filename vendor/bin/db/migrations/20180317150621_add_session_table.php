<?php

use Phinx\Migration\AbstractMigration;

class AddSessionTable extends AbstractMigration
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
        $table = $this->table('session_storage',['id'=>false,'primary_key'=>['id','name']]);
        $table->addColumn('id','integer',['length'=>10,'signed'=>false,'identity'=>true])
            ->addColumn('name','string')
            ->addColumn('modified','integer',['null'=>true])
            ->addColumn('lifetime','integer',['null'=>true])
            ->addColumn('data','text')
            ->create();
    }
}
