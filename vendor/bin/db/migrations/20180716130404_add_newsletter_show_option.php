<?php

use Phinx\Migration\AbstractMigration;

class AddNewsletterShowOption extends AbstractMigration
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
        $this->execute("update template_option set sort_order='5' where template_option_id=83");
        $this->insert('template_option',[
            [
                'template_id'=>3,
                'label'=>'Show Newsletter Signup Form',
                'key'=>'show_newsletter',
                'type'=>'radio',
                'group'=>'footer',
                'sort_order'=>'4',
                'options'=>'1=Yes,0=No',
                'value'=>'1'
            ],
        ]);
    }
}
