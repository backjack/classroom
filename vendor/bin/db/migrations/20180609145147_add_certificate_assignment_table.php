<?php

use Phinx\Migration\AbstractMigration;

class AddCertificateAssignmentTable extends AbstractMigration
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
        $table = $this->table('certificate_assignment', array('id' => false, 'primary_key' => array('certificate_id', 'assignment_id')));
        $table->addColumn('certificate_id', 'integer')
            ->addColumn('assignment_id','integer')
            ->addForeignKey('certificate_id','certificate','certificate_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('assignment_id','assignment','assignment_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }
}
