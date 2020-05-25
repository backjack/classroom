<?php

use Phinx\Migration\AbstractMigration;

class AddAssignmentPermissions extends AbstractMigration
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
            'permission_group_id'=>15,
            'group'=>'Homework',
            'sort_order'=>15
        ];
        $this->insert('permission_group',$data);

        $data = [
            [
                'permission'=>'add_homework',
                'path'=>'assignment/add',
                'permission_group_id'=>15
            ],
            [
                'permission'=>'view_homework_list',
                'path'=>'assignment/index',
                'permission_group_id'=>15
            ],
            [
                'permission'=>'edit_homework',
                'path'=>'assignment/edit',
                'permission_group_id'=>15
            ],
            [
                'permission'=>'view_homework',
                'path'=>'assignment/view',
                'permission_group_id'=>15
            ],
            [
                'permission'=>'delete_homework',
                'path'=>'assignment/delete',
                'permission_group_id'=>15
            ],
            [
                'permission'=>'view_homework_submissions',
                'path'=>'assignment/submissions',
                'permission_group_id'=>15
            ],
            [
                'permission'=>'view_homework_submission',
                'path'=>'assignment/viewsubmission',
                'permission_group_id'=>15
            ],
            [
                'permission'=>'export_homework_results',
                'path'=>'assignment/exportresult',
                'permission_group_id'=>15
            ],
        ];



        $this->insert('permission',$data);

        $rowset = $this->query("SELECT * FROM permission where permission_group_id=15");
        foreach($rowset as $row){

            $rowset2 = $this->query("SELECT * FROM role where role_id < 2");
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
