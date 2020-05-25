<?php

use Phinx\Migration\AbstractMigration;

class AddNotificationPermissions extends AbstractMigration
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
                'permission_id'=>133,
                'permission'=>'view_email_notifications',
                'path'=>'messages/emails',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>134,
                'permission'=>'edit_email_notification',
                'path'=>'messages/editemail',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>135,
                'permission'=>'view_sms_notifications',
                'path'=>'messages/sms',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>136,
                'permission'=>'edit_sms_notification',
                'path'=>'messages/editsms',
                'permission_group_id'=>9
            ]


        ]) ;

        $rowset = $this->query("SELECT * FROM permission where permission_id > 132");
        foreach($rowset as $row){

            $rowset2 = $this->query("SELECT * FROM role where role_id = 1");
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
