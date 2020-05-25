<?php

use Phinx\Migration\AbstractMigration;

class SendClassReminder extends AbstractMigration
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
        $data = [
            [
                'key'=>'general_send_reminder',
                'label'=>'Send Class Reminders',
                'value'=>1,
                'type'=>'radio',
                'options'=>'1=Yes,0=No'
            ],
            [
                'key'=>'general_reminder_days',
                'label'=>'Reminder Day',
                'placeholder'=>'How many days to a class should a reminder be sent',
                'value'=>'1',
                'class'=>'number',
                'type'=>'text',
            ]

        ];

        $this->insert('setting',$data);
    }
}
