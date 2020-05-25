<?php

use Phinx\Migration\AbstractMigration;

class AddLiveChatSetting extends AbstractMigration
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
          'key'=>'general_chat_code',
            'label'=>'Live Chat Code',
            'placeholder'=>'Enter in the code from your live chat provider here',
            'type'=>'textarea',
        ];
        $this->insert('setting',$data);
    }
}
