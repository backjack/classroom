<?php

use Phinx\Migration\AbstractMigration;

class AddDiscussPermission extends AbstractMigration
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
                'group'=>'Discussions',
                'sort_order'=>11
            ]
        ];
        $this->insert('permission_group',$data);

        $data = [
            [
                'permission'=>'view_discussions',
                'path'=>'discuss/index',
                'permission_group_id'=>11
            ],
            [
                'permission'=>'view_discussion',
                'path'=>'discuss/viewdiscussion',
                'permission_group_id'=>11
            ],
            [
                'permission'=>'reply_discussion',
                'path'=>'discuss/addreply',
                'permission_group_id'=>11
            ],
            [
                'permission'=>'delete_discussion',
                'path'=>'discuss/delete',
                'permission_group_id'=>11
            ],



        ];
        $this->insert('permission',$data);


        $rowset = $this->query("SELECT * FROM permission where permission_group_id=11");
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
