<?php

use Phinx\Migration\AbstractMigration;

class AddTemplatePermission extends AbstractMigration
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
                   'permission_id'=>112,
                   'permission'=>'view_themes',
                   'path'=>'template/index',
                   'permission_group_id'=>9
               ],
               [
                   'permission_id'=>113,
                   'permission'=>'customize_theme',
                   'path'=>'template/customize',
                   'permission_group_id'=>9
               ],
               [
                   'permission_id'=>114,
                   'permission'=>'install_theme',
                   'path'=>'template/install',
                   'permission_group_id'=>9
               ]

           ]) ;

        //add to roles

        $rowset = $this->query("SELECT * FROM permission where permission_id > 111");
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
