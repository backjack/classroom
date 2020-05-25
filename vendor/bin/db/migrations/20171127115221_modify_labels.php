<?php

use Phinx\Migration\AbstractMigration;

class ModifyLabels extends AbstractMigration
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
        $this->execute("UPDATE setting set setting.key='label_sessions',label='Upcoming Sessions' where setting_id=57");
        $this->execute("UPDATE setting set label='Certificates' where setting_id=79");

        $this->insert('setting',[
           [
               'key'=>'label_courses',
               'label'=>'Online Courses',
               'type'=>'text',
           ],
            [
                'key'=>'label_my_sessions',
                'label'=>'My Sessions & Courses',
                'type'=>'text',
            ],
            [
                'key'=>'label_homework',
                'label'=>'Homework',
                'type'=>'text',
            ],

        ]);


    }
}
