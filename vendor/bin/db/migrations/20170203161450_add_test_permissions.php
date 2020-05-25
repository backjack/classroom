<?php

use Phinx\Migration\AbstractMigration;

class AddTestPermissions extends AbstractMigration
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
                  'group'=>'Tests',
                  'sort_order'=>10
              ]
            ];
        $this->insert('permission_group',$data);

        $data = [
            [
                'permission'=>'view_tests',
                'path'=>'test/index',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'add_test',
                'path'=>'test/add',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'add_options',
                'path'=>'test/addoptions',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'add_question',
                'path'=>'test/addquestion',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'delete_question',
                'path'=>'test/delete',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'delete_option',
                'path'=>'test/deleteoption',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'delete_question',
                'path'=>'test/deletequestion',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'duplicate_question',
                'path'=>'test/duplicate',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'edit_question',
                'path'=>'test/edit',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'edit_option',
                'path'=>'test/editoption',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'edit_question',
                'path'=>'test/editquestion',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'export_result',
                'path'=>'test/exportresult',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'manage_questions',
                'path'=>'test/questions',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'view_results',
                'path'=>'test/results',
                'permission_group_id'=>10
            ],
            [
                'permission'=>'view_result',
                'path'=>'test/testresult',
                'permission_group_id'=>10
            ],
          

        ];
        $this->insert('permission',$data);


    }
}
