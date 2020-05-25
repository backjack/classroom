<?php

use Phinx\Migration\AbstractMigration;

class AddReportPermissions extends AbstractMigration
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
                'permission_id'=>128,
                'permission'=>'view_report_page',
                'path'=>'report/index',
                'permission_group_id'=>16
            ],
            [
                'permission_id'=>129,
                'permission'=>'view_class_report',
                'path'=>'report/classes',
                'permission_group_id'=>16
            ],
            [
                'permission_id'=>130,
                'permission'=>'view_students_report',
                'path'=>'report/students',
                'permission_group_id'=>16
            ],
            [
                'permission_id'=>131,
                'permission'=>'view_tests_report',
                'path'=>'report/tests',
                'permission_group_id'=>16
            ],
            [
                'permission_id'=>132,
                'permission'=>'view_homework_report',
                'path'=>'report/homework',
                'permission_group_id'=>16
            ],


        ]) ;

        $rowset = $this->query("SELECT * FROM permission where permission_id > 127");
        foreach($rowset as $row){

            $rowset2 = $this->query("SELECT * FROM role where role_id < 3");
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
