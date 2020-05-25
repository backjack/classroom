<?php

use Phinx\Migration\AbstractMigration;

class AddAccountIdToResources extends AbstractMigration
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
        $table = $this->table('session');
        $table->addColumn('account_id','integer',['null'=>true])
            ->update();

        $table = $this->table('lesson');
        $table->addColumn('account_id','integer',['null'=>true])
            ->update();

        $table = $this->table('homework');
        $table->addColumn('account_id','integer',['null'=>true])
            ->update();

        $table = $this->table('download');
        $table->addColumn('account_id','integer',['null'=>true])
            ->update();


        $table = $this->table('test');
        $table->addColumn('account_id','integer',['null'=>true])
            ->update();

        $table = $this->table('certificate');
        $table->addColumn('account_id','integer',['null'=>true])
            ->update();

        $table = $this->table('newsflash');
        $table->addColumn('account_id','integer',['null'=>true])
            ->update();




    }
}
