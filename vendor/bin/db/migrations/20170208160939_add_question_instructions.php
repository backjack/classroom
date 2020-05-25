<?php

use Phinx\Migration\AbstractMigration;

class AddQuestionInstructions extends AbstractMigration
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
            [
                'key'=>'general_discussion_instructions',
                'label'=>'Discussion Instructions',
                'type'=>'textarea',
                'class'=>'rte',
                'value'=>'<p>Please ask your question and we will reply you soon!</p>'
            ]
        ];

        $this->insert('setting',$data);
    }
}
