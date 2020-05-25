<?php

use Phinx\Migration\AbstractMigration;

class AddRolesMigration extends AbstractMigration
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

        $rows = [
            [
                'permission_group_id'    =>  9,
                'permission'  => 'edit_site_settings',
                'path' => 'setting/index'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'view_roles',
                'path' => 'setting/role'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'add_role',
                'path' => 'setting/addrole'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'edit_role',
                'path' => 'setting/editrole'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'delete_role',
                'path' => 'setting/deleterole'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'view_payment_methods',
                'path' => 'payment/index'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'edit_payment_methods',
                'path' => 'payment/edit'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'view_admins',
                'path' => 'setting/admins'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'add_admin',
                'path' => 'setting/addadmin'
            ],
            [
                'permission_group_id'    =>  9,
                'permission'  => 'edit_admin',
                'path' => 'setting/editadmin'
            ],



        ];

        // this is a handy shortcut
        $this->insert('permission', $rows);

    }
}
