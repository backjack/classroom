<?php

use Phinx\Migration\AbstractMigration;

class AddCertificateMenuOption extends AbstractMigration
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
        $data=[
            [
                'key'=>'menu_show_certificates',
                'label'=>'Show Certificates & Letters',
                'type'=>'radio',
                'value'=>'1',
                'options'=>'1=Yes,0=No',

            ]
        ];

        $this->insert('setting',$data);
    }
}
