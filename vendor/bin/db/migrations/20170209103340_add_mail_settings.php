<?php

use Phinx\Migration\AbstractMigration;

class AddMailSettings extends AbstractMigration
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
              'key'=>'mail_protocol',
              'label'=>'Mail Protocol',
              'type'=>'select',
              'value'=>'mail',
              'options'=>'mail=Mail,smtp=SMTP'
          ],
            [
                'key'=>'mail_smtp_host',
                'label'=>'SMTP Host',
                'type'=>'text'
            ],
            [
                'key'=>'mail_smtp_username',
                'label'=>'SMTP Username',
                'type'=>'text',
            ],
            [
                'key'=>'mail_smtp_password',
                'label'=>'SMTP Password',
                'type'=>'text'
            ],
            [
                'key'=>'mail_smtp_port',
                'label'=>'SMTP Port',
                'type'=>'text'
            ],
            [
                'key'=>'mail_smtp_timeout',
                'label'=>'SMTP Timeout',
                'type'=>'text'
            ],
            [
                'key'=>"general_show_fee",
                'label'=>'Show Session Fees',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'value'=>1
            ],
            [
                'key'=>'menu_show_discussions',
                'label'=>'Show Discussions',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'value'=>1
            ],
            [
                'key'=>'menu_show_tests',
                'label'=>'Show Tests',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'value'=>1
            ],
            [
                'key'=>'menu_show_notes',
                'label'=>'Show Revision Notes',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'value'=>1
            ],
            [
                'key'=>'menu_show_attended',
                'label'=>'Show Classes Attended',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'value'=>1
            ],

        ];

        $this->insert('setting',$data);

    }
}
