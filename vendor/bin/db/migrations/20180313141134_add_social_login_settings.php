<?php

use Phinx\Migration\AbstractMigration;

class AddSocialLoginSettings extends AbstractMigration
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
                'key'=>'social_enable_facebook',
                'label'=>'Enable Facebook Login',
                'type'=>'radio',
                'options'=> '1=Yes,0=No',
            ],
            [
                'key'=>'social_facebook_secret',
                'label'=>'Facebook App Secret',
                'type'=>'text',
            ],
            [
                'key'=>'social_facebook_app_id',
                'label'=>'Facebook App ID',
                'type'=>'text',
            ],
            [
                'key'=>'social_enable_google',
                'label'=>'Enable Google Login',
                'type'=>'radio',
                'options'=> '1=Yes,0=No',
            ],
            [
                'key'=>'social_google_secret',
                'label'=>'Google App Secret',
                'type'=>'text',
            ],
            [
                'key'=>'social_google_id',
                'label'=>'Google ID',
                'type'=>'text',
            ],




        ]);
    }
}
