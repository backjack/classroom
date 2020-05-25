<?php

use Phinx\Migration\AbstractMigration;

class AddSessionTests extends AbstractMigration
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
        $table = $this->table('session_test',['primary'=>'session_test_id']);
        $table->addColumn('session_id','integer',['limit'=>10,'signed'=>false])
            ->addColumn('test_id','integer')
            ->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('test_id','test','test_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addColumn('opening_date','integer',['null'=>true])
            ->addColumn('closing_date','integer',['null'=>true])
             ->create();

        $table = $this->table('test');
        $table->addColumn('private','boolean',['default'=>0])
            ->update();

        //get all test records where session_id is greater than zero
        $rowset = $this->query("SELECT * FROM test where session_id > 0");
        foreach($rowset as $row){

            $this->insert('session_test',[
                'session_id'=>$row['session_id'],
                'test_id'=>$row['test_id'],
            ]);
            $this->execute('UPDATE test SET private=1 where test_id='.$row['test_id']);
        }

        //drop the session_id column
        $table = $this->table('test');
        $table->removeColumn('session_id')
            ->update();
    }
}
