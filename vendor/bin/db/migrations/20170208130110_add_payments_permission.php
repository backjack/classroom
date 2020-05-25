<?php

use Phinx\Migration\AbstractMigration;

class AddPaymentsPermission extends AbstractMigration
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
        $data = [
            [
                'permission'=>'view_payments',
                'path'=>'student/payments',
                'permission_group_id'=>2
            ]
        ];

        $this->insert('permission',$data);

        $rowset= $this->query('SELECT * FROM permission where path=\'student/payments\'');
        $row = $rowset->fetch(PDO::FETCH_ASSOC);
        $id = $row['permission_id'];


        //get accounts
        $rowset = $this->query("SELECT * FROM role");
        foreach($rowset as $row){
            $this->insert('role_permission',[
                [
                    'role_id'=>$row['role_id'],
                    'permission_id'=>$id
                ]
            ]);
        }
    }
}
