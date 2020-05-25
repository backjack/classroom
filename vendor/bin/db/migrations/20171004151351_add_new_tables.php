<?php

use Phinx\Migration\AbstractMigration;

class AddNewTables extends AbstractMigration
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
        $table = $this->table('lecture');
        $table->removeColumn('content')
            ->removeColumn('video_code')
            ->update();

        $table = $this->table('session');
        $table->addColumn('effort','string',['null'=>true])
            ->addColumn('length','string',['null'=>true])
            ->addColumn('short_description','text',['null'=>true])
            ->addColumn('introduction','text',['null'=>true])
            ->update();

    }
}
