<?php

use Phinx\Migration\AbstractMigration;

class AddBannerSettings extends AbstractMigration
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
                'key'=>'banner_status',
                'label'=>'Enable Banner',
                'type'=>'radio',
                'value'=>0,
                'options'=>'1=Yes,0=No'
            ],
            [
                'key'=>'banner_app_name',
                'label'=>'App Name',
                'type'=>'text',
            ],
            [
                'key'=>'banner_android_id',
                'label'=>'Android ID',
                'type'=>'text',
            ],
            [
                'key'=>'banner_ios_id',
                'label'=>'iOS ID',
                'type'=>'text',
            ],
            [
                'key'=>'banner_icon_url',
                'label'=>'Icon URL',
                'type'=>'text',
            ]

        ]);
    }
}
