<?php

use Phinx\Migration\AbstractMigration;

class AddTestGradesTable extends AbstractMigration
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
        $table = $this->table('test_grade',['id'=>'test_grade_id']);
        $table->addColumn('grade','string');
        $table->addColumn('min','integer');
        $table->addColumn('max','integer');
        $table->create();

        $this->insert('test_grade',[
           [
               'grade'=>'A',
               'min'=>'70',
               'max'=>'100'
           ],
            [
                'grade'=>'B',
                'min'=>'60',
                'max'=>'69'
            ],
            [
                'grade'=>'C',
                'min'=>'50',
                'max'=>'59',
            ],
            [
                'grade'=>'D',
                'min'=>'45',
                'max'=>'49'
            ],
            [
                'grade'=>'E',
                'min'=>'40',
                'max'=>'44'
            ],
            [
                'grade'=>'F',
                'min'=>'0',
                'max'=>'39'
            ]
        ]);
    }
}
