<?php

use Phinx\Migration\AbstractMigration;

class AddCertificateFieldTables extends AbstractMigration
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
        $table = $this->table('certificate_field',['id'=>'certificate_field_id']);
            $table->addColumn('certificate_id', 'integer')
            ->addColumn('certificate_field_type_id', 'integer')
            ->addColumn('position_x', 'integer')
                ->addColumn('position_y', 'integer')
                ->addColumn('width', 'integer')
                ->addColumn('height', 'integer')
                ->addColumn('font_size', 'integer')
                ->addForeignKey('certificate_id','certificate','certificate_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                ->addForeignKey('certificate_field_type_id','certificate_field_type','certificate_field_type_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
                  ->create();


        $table = $this->table('certificate_session', array('id' => false, 'primary_key' => array('certificate_id', 'session_id')));
        $table->addColumn('certificate_id', 'integer')
            ->addColumn('session_id','integer',['limit'=>10,'signed'=>false])
            ->addForeignKey('certificate_id','certificate','certificate_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('session_id','session','session_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();


        $table = $this->table('certificate_lesson', array('id' => false, 'primary_key' => array('certificate_id', 'lesson_id')));
        $table->addColumn('certificate_id', 'integer')
            ->addColumn('lesson_id','integer',['limit'=>10,'signed'=>false])
            ->addForeignKey('certificate_id','certificate','certificate_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->addForeignKey('lesson_id','lesson','lesson_id',['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();


    }
}
