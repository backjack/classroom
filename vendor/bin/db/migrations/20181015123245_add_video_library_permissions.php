<?php

use Phinx\Migration\AbstractMigration;

class AddVideoLibraryPermissions extends AbstractMigration
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
        $this->insert('permission_group',[
           'permission_group_id'=>17,
            'group'=>'Video Library',
            'sort_order'=>'17'
        ]);


        $this->insert('permission',[
            [
                'permission_id'=>137,
                'permission'=>'view_videos',
                'path'=>'video/index',
                'permission_group_id'=>17
            ],
            [
                'permission_id'=>138,
                'permission'=>'add_video',
                'path'=>'video/add',
                'permission_group_id'=>17
            ],
            [
                'permission_id'=>139,
                'permission'=>'edit_video',
                'path'=>'video/edit',
                'permission_group_id'=>17
            ],
            [
                'permission_id'=>140,
                'permission'=>'delete_video',
                'path'=>'video/delete',
                'permission_group_id'=>17
            ],
            [
                'permission_id'=>141,
                'permission'=>'play_video',
                'path'=>'video/play',
                'permission_group_id'=>17
            ],
            [
                'permission_id'=>142,
                'permission'=>'view_video_space',
                'path'=>'video/disk',
                'permission_group_id'=>17
            ]


        ]) ;

        $rowset = $this->query("SELECT * FROM permission where permission_id > 136");
        foreach($rowset as $row){

            $rowset2 = $this->query("SELECT * FROM role where role_id < 4");
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
