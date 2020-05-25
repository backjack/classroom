<?php

use Phinx\Migration\AbstractMigration;

class AddDefaultGateways extends AbstractMigration
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
        $this->insert('setting',[
            [
                'key'=>'sms_enabled',
                'label'=>'Enable SMS?',
                'value'=>0,
                'type'=>'checkbox',
            ],
            [
                'key'=>'sms_sender_name',
                'label'=>'Sender name',
                'value'=>'',
                'type'=>'text',
            ]

        ]);

        $this->insert('sms_gateway',[
            [
                'sms_gateway_id'=>1,
                'gateway_name'=>'Smart SMS Solutions',
                'url'=>'http://smartsmssolutions.com',
                'country'=>'Nigeria',
                'code'=>'smartsms',
                'about'=>'Smart SMS Solutions is one of the most affordable sms gateways in the Nigerian market. However they only support sending texts within Nigeria'
            ],
            [
                'sms_gateway_id'=>2,
                'gateway_name'=>'Cheap global sms',
                'url'=>'https://cheapglobalsms.com',
                'country'=>'Nigeria',
                'code'=>'cheapglobal',
                'about'=>'Cheap global sms has affordable rates. They also support sending SMS internationally'
            ],

        ]);


        $this->insert('sms_gateway_field',[
           [
               'sms_gateway_id'=>1,
               'label'=>'Username',
               'key'=>'username',
               'type'=>'text',
               'sort_order'=>1,
               'placeholder'=>'Your login username'
           ],
            [
                'sms_gateway_id'=>1,
                'label'=>'Password',
                'key'=>'password',
                'type'=>'text',
                'sort_order'=>2,
            ],
            [
                'sms_gateway_id'=>2,
                'label'=>'Sub Account',
                'key'=>'sub_account',
                'type'=>'text',
                'sort_order'=>1,
                'placeholder'=>'Your sub account'
            ],
            [
                'sms_gateway_id'=>2,
                'label'=>'Sub Account Password',
                'key'=>'sub_account_pass',
                'type'=>'text',
                'sort_order'=>2,
                'placeholder'=>'Your sub account password'
            ],

        ]);



    }
}
