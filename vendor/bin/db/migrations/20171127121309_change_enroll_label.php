<?php

use Phinx\Migration\AbstractMigration;

class ChangeEnrollLabel extends AbstractMigration
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
        $this->execute("UPDATE setting set label='Enroll' where setting_id=51");
        $this->insert('setting',[
           'key'=>'regis_confirm_email',
            'label'=>'Confirm Student Emails',
            'value'=>0,
            'type'=>'radio',
            'options'=>'1=Yes,0=No'
        ]);
    }
}
