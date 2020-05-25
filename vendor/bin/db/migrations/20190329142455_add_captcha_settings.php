<?php

use Phinx\Migration\AbstractMigration;

class AddCaptchaSettings extends AbstractMigration
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
                'key'=>'regis_captcha_type',
                'label'=>'Captcha Type',
                'type'=>'select',
                'value'=>'image',
                'options'=>'Image,Google reCAPTCHA v3'
            ],
            [
                'key'=>'regis_recaptcha_key',
                'label'=>'Recaptcha Site Key',
                'type'=>'text',
            ],
            [
                'key'=>'regis_recaptcha_secret',
                'label'=>'Recaptcha Secret Key',
                'type'=>'text',
            ],


        ]);
    }
}
