<?php

use Phinx\Migration\AbstractMigration;

class AddDownloadTables extends AbstractMigration
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
        $table = $this->table('download',['id'=>'download_id']);
        $table->addColumn('download_name', 'string', array('limit' => 250))
            ->addColumn('created_on', 'integer')
            ->addColumn('status', 'boolean')
            ->addColumn('description','text')
            ->create();

        $table = $this->table('download_file',['id'=>'download_file_id']);
        $table->addColumn('download_id', 'integer')
            ->addColumn('path', 'text')
            ->addColumn('created_on', 'integer')
            ->addColumn('status', 'boolean')
            ->addForeignKey('download_id','download','download_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();

        $table = $this->table('download_session',['id'=>'download_session_id']);
        $table->addColumn('download_id','integer')
                ->addColumn('session_id','integer',['limit'=>10,'signed'=>false])
            ->addForeignKey('download_id','download','download_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();


    }
}
