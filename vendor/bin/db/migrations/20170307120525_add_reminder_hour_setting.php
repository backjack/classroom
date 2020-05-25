<?php

use Phinx\Migration\AbstractMigration;

class AddReminderHourSetting extends AbstractMigration
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
        $options='';
        foreach(range(0,23) as $val){
            $options .= $val.',';
        }

        $len = strlen($options);
        $options = substr($options,0,$len-1);

        $data = [
            [
                'key'=>'general_reminder_hour',
                'label'=>'Reminder Hour',
                'value'=>'12',
                'type'=>'select',
                'options'=>$options
            ]
        ];

        $this->insert('setting',$data);
    }
}
