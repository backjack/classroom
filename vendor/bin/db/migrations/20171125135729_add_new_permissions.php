<?php

use Phinx\Migration\AbstractMigration;

class AddNewPermissions extends AbstractMigration
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
            'permission'=>'add_course',
            'path'=>'session/addcourse',
            'permission_group_id'=>2,
        ],
            [
                'permission'=>'view_course_categories',
                'path'=>'session/groups',
                'permission_group_id'=>2,
            ],
            [
                'permission'=>'add_course_category',
                'path'=>'session/addgroup',
                'permission_group_id'=>2,
            ],
            [
                'permission'=>'edit_course_category',
                'path'=>'session/editgroup',
                'permission_group_id'=>2,
            ],
            [
                'permission'=>'delete_course_category',
                'path'=>'session/deletegroup',
                'permission_group_id'=>2,
            ],
            [
                'permission'=>'view_class_groups',
                'path'=>'lesson/groups',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'add_class_group',
                'path'=>'lesson/addgroup',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'edit_class_group',
                'path'=>'lesson/editgroup',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'delete_class_group',
                'path'=>'lesson/deletegroup',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'manage_class_downloads',
                'path'=>'lesson/files',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'view_lectures',
                'path'=>'lecture/index',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'add_lecture',
                'path'=>'lecture/add',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'edit_lecture',
                'path'=>'lecture/edit',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'delete_lecture',
                'path'=>'lecture/delete',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'manage_lecture_downloads',
                'path'=>'lecture/files',
                'permission_group_id'=>4,
            ],
            [
                'permission'=>'manage_lecture_content',
                'path'=>'lecture/content',
                'permission_group_id'=>4,
            ],
        ];

        $this->insert('permission',$data);

        $rowset = $this->query("SELECT * FROM permission where permission_group_id=2 OR permission_group_id=4");
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
