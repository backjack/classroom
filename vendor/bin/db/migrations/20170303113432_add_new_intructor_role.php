<?php

use Phinx\Migration\AbstractMigration;

class AddNewIntructorRole extends AbstractMigration
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
        $data =[
            'role'=>'Instructor'
        ];
        $this->insert('role',$data);
        $rowset = $this->query('select * from role order by role_id desc limit 0,1');
        $row ='';
        foreach($rowset as $val){
            $row = $val;
        }

        $roleId  = $row['role_id'];

        $data = [
          [
              'role_id'=>$roleId,
              'permission_id'=>1
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>3
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>15
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>16
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>17
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>24
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>25
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>26
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>72
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>73
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>14
          ],
            [
                'role_id'=>$roleId,
                'permission_id'=>13
          ],

        ];

        $this->insert('role_permission',$data);
    }
}
