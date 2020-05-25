<?php

use Phinx\Migration\AbstractMigration;

class AddSmsGatewayPermissions extends AbstractMigration
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
                'permission_id'=>115,
                'permission'=>'configure_sms_gateways',
                'path'=>'smsgateway/index',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>116,
                'permission'=>'edit_sms_gateway',
                'path'=>'smsgateway/customize',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>117,
                'permission'=>'install_gateway',
                'path'=>'smsgateway/install',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>118,
                'permission'=>'uninstall_gateway',
                'path'=>'smsgateway/uninstall',
                'permission_group_id'=>9
            ]

        ]) ;

        //add to roles

        $rowset = $this->query("SELECT * FROM permission where permission_id > 114");
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
