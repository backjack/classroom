<?php

use Phinx\Migration\AbstractMigration;

class AddForumPermissions extends AbstractMigration
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
                'permission_id'=>119,
                'permission'=>'view_forum_topics',
                'path'=>'forum/index',
                'permission_group_id'=>11
            ],
            [
                'permission_id'=>120,
                'permission'=>'add_forum_topic',
                'path'=>'forum/addtopic',
                'permission_group_id'=>11
            ],
            [
                'permission_id'=>121,
                'permission'=>'view_forum_topic',
                'path'=>'forum/topic',
                'permission_group_id'=>11
            ],
            [
                'permission_id'=>122,
                'permission'=>'reply_forum_topic',
                'path'=>'forum/reply',
                'permission_group_id'=>11
            ],
            [
                'permission_id'=>123,
                'permission'=>'delete_forum_topic',
                'path'=>'forum/deletetopic',
                'permission_group_id'=>11
            ]

        ]) ;

        //add to roles

        $rowset = $this->query("SELECT * FROM permission where permission_id > 118");
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
