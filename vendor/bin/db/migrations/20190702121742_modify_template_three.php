<?php

use Phinx\Migration\AbstractMigration;

class ModifyTemplateThree extends AbstractMigration
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
                'template_id'=>3,
                'label'=>'Show About Us in footer',
                'key'=>'show_footer_about',
                'type'=>'radio',
                'group'=>'footer',
                'sort_order'=>'6',
                'options'=>'1=Yes,0=No',
                'value'=>'1'
            ],
            [
                'template_id'=>3,
                'label'=>'Footer About Us',
                'key'=>'footer_about_us',
                'type'=>'textarea',
                'group'=>'home_page',
                'sort_order'=>'7',
            ],
            [
                'template_id'=>3,
                'label'=>'Show Contact Us in footer',
                'key'=>'show_footer_contact',
                'type'=>'radio',
                'group'=>'footer',
                'sort_order'=>'8',
                'options'=>'1=Yes,0=No',
                'value'=>'1'
            ],
            [
                'template_id'=>3,
                'label'=>'Address',
                'key'=>'footer_address',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'9',
            ],
            [
                'template_id'=>3,
                'label'=>'Email',
                'key'=>'footer_email',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'10',
            ],
            [
                'template_id'=>3,
                'label'=>'Telephone',
                'key'=>'footer_tel',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'11',
            ],
        ]);
    }
}
