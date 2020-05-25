<?php

use Phinx\Migration\AbstractMigration;

class AddCertifcateTable extends AbstractMigration
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
        $table = $this->table('certificate',['id'=>'certificate_id']);
        $table->addColumn('certificate_name', 'string', array('limit' => 250))
            ->addColumn('certificate_image', 'string', array('limit' => 250))
            ->addColumn('created_on', 'integer')
            ->addColumn('status', 'boolean')
            ->create();

        $table = $this->table('certificate_field_type',['id'=>'certificate_field_type_id']);
        $table->addColumn('field_name','string',['limit'=>250])
            ->create();

        $data =[
            [
                'field_name'=>'Student Name',
            ],
            [
                'field_name'=>'Session Name',
            ],
            [
                'field_name'=>'Session Start Date',
            ],
            [
                'field_name'=>'Session End Date',
            ],
            [
                'field_name'=>'Company Name',
            ],
            [
                'field_name'=>'Date Generated',
            ]
        ];

        $this->insert('certificate_field_type', $data);

    }
}
