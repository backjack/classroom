<?php

use Phinx\Migration\AbstractMigration;

class AddTestGradePermission extends AbstractMigration
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

        $this->insert('permission',[
            [
                'permission_id'=>124,
                'permission'=>'view_test_grades',
                'path'=>'setting/testgrades',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>125,
                'permission'=>'add_test_grade',
                'path'=>'setting/addtestgrade',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>126,
                'permission'=>'edit_test_grade',
                'path'=>'setting/edittestgrade',
                'permission_group_id'=>9
            ], 
            [
                'permission_id'=>127,
                'permission'=>'delete_test_grade',
                'path'=>'setting/deletetestgrade',
                'permission_group_id'=>9
            ],
            

        ]) ;

        //add to roles

        $rowset = $this->query("SELECT * FROM permission where permission_id > 123");
        foreach($rowset as $row){

            $rowset2 = $this->query("SELECT * FROM role where role_id=1");
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
