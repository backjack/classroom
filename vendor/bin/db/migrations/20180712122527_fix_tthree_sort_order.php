<?php

use Phinx\Migration\AbstractMigration;

class FixTthreeSortOrder extends AbstractMigration
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
        $this->execute('update template_option set sort_order=5,value=1 where template_option_id=54');
        $this->execute("update template_option set template_option.group='home_page_reviews' where template_option.group='home_page' and template_option_id > 57 and template_id=3");
    }
}
