<?php

use Phinx\Migration\AbstractMigration;

class AddNewSettings extends AbstractMigration
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
                'key'=>'general_address',
                'label'=>'Contact Address',
                'type'=>'textarea',
                'class'=>'rte',
                'value'=>$this->getOptionValue('footer_address')
            ],
            [
                'key'=>'general_tel',
                'label'=>'Contact Telephone No',
                'type'=>'text',
                'value'=>$this->getOptionValue('footer_tel')
            ],
            [
                'key'=>'general_contact_email',
                'label'=>'Contact Email',
                'type'=>'text',
                'value'=>$this->getOptionValue('footer_email')
            ],


        ]);
    }

    private function getOptionValue($option){
        $row = $this->fetchRow("select * from setting where setting.key='$option'");

        return $row['value'];

    }
}
