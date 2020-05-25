<?php

use Phinx\Migration\AbstractMigration;

class AddSurveryPermissions extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/lasurvey/migrations.html#the-abstractmigration-class
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
            'permission_group_id'=>17,
            'group'=>'Survey',
            'sort_order'=>17
        ];
        $this->insert('permission_group',$data);

        $data = [
            [
                'permission'=>'view_surveys',
                'path'=>'survey/index',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'add_survey',
                'path'=>'survey/add',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'add_options',
                'path'=>'survey/addoptions',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'add_question',
                'path'=>'survey/addquestion',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'delete_question',
                'path'=>'survey/delete',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'delete_option',
                'path'=>'survey/deleteoption',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'delete_question',
                'path'=>'survey/deletequestion',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'duplicate_question',
                'path'=>'survey/duplicate',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'edit_question',
                'path'=>'survey/edit',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'edit_option',
                'path'=>'survey/editoption',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'edit_question',
                'path'=>'survey/editquestion',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'export_result',
                'path'=>'survey/exportresult',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'manage_questions',
                'path'=>'survey/questions',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'view_results',
                'path'=>'survey/results',
                'permission_group_id'=>17
            ],
            [
                'permission'=>'view_result',
                'path'=>'survey/surveyresult',
                'permission_group_id'=>17
            ],
        ];



        $this->insert('permission',$data);

        $rowset = $this->query("SELECT * FROM permission where permission_group_id=17");
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
