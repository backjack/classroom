<?php

use Phinx\Migration\AbstractMigration;

class AddTestPermissionToRole extends AbstractMigration
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
        $rowset = $this->query("SELECT * FROM permission where permission_group_id=10");
        foreach($rowset as $row){

            $rowset2 = $this->query("SELECT * FROM role");
            foreach($rowset2 as $row2){
                $this->insert('role_permission',[
                    [
                        'role_id'=>$row2['role_id'],
                        'permission_id'=>$row['permission_id']
                    ]
                ]);
            }

        }

    }
}