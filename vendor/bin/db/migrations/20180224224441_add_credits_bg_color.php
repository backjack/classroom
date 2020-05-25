<?php

use Phinx\Migration\AbstractMigration;

class AddCreditsBgColor extends AbstractMigration
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
        $this->insert('template_option',[
            [
                'template_id'=>2,
                'label'=>'Credits background color',
                'key'=>'footer_credits_bgcolor',
                'type'=>'color',
                'group'=>'footer',
                'sort_order'=>'10',
            ],
            [
                'template_id'=>2,
                'label'=>'Credits text color',
                'key'=>'footer_credits_textcolor',
                'type'=>'color',
                'group'=>'footer',
                'sort_order'=>'11',
            ],
        ]);
    }
}
