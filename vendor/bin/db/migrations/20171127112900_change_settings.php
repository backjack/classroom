<?php

use Phinx\Migration\AbstractMigration;

class ChangeSettings extends AbstractMigration
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
             $this->execute("UPDATE setting set setting.key='menu_show_courses',label='Show Online Courses' WHERE setting_id=34");
             $this->execute("UPDATE setting set setting.key='menu_show_sessions',label='Show Upcoming Sessions' WHERE setting_id=35");
             $this->execute("UPDATE setting set label='Show Certificates' WHERE setting_id=80");

        $data = [
                'key'=>'menu_show_homework',
                'label'=>'Show Homework',
                'type'=>'radio',
                'value'=>'1',
                'options'=>'1=Yes,0=No',
            ];
        $this->insert('setting',$data);

    }
}













