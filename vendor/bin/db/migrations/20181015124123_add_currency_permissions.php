<?php

use Phinx\Migration\AbstractMigration;

class AddCurrencyPermissions extends AbstractMigration
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
                'permission_id'=>143,
                'permission'=>'view_coupons',
                'path'=>'payment/coupons',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>144,
                'permission'=>'add_coupon',
                'path'=>'payment/addcoupon',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>145,
                'permission'=>'edit_coupon',
                'path'=>'payment/editcoupon',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>146,
                'permission'=>'delete_coupon',
                'path'=>'payment/deletecoupon',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>147,
                'permission'=>'manage_currencies',
                'path'=>'setting/currencies',
                'permission_group_id'=>9
            ],
            [
                'permission_id'=>148,
                'permission'=>'delete_currencies',
                'path'=>'setting/deletecurrency',
                'permission_group_id'=>9
            ]


        ]) ;

        $rowset = $this->query("SELECT * FROM permission where permission_id > 142");
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
