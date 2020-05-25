<?php

use Phinx\Migration\AbstractMigration;

class AddFeaturedLabel extends AbstractMigration
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
                'key'=>'label_featured',
                'label'=>'Featured',
                'type'=>'text',
            ],
            [
                'key'=>'label_calendar',
                'label'=>'Calendar',
                'type'=>'text',
            ],
            [
                'key'=>'label_blog_posts',
                'label'=>'Blog Posts',
                'type'=>'text',
            ],
            [
                'key'=>'label_register',
                'label'=>'Register',
                'type'=>'text',
            ],

        ]);
    }
}
