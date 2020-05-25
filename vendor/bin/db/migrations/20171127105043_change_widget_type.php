<?php

use Phinx\Migration\AbstractMigration;

class ChangeWidgetType extends AbstractMigration
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
        $total= $this->execute('DELETE FROM widget_value WHERE widget_id=3');
        $form= '<div class="form-group">
    <label for="">Select Session/Course</label>
    [sessionselect]
</div>';
        $total = $this->execute("UPDATE widget SET widget_name='Featured',widget_code='sessions',widget_description='Select sessions/courses to feature on the homepage',form='$form' WHERE widget_id=3");

    }
}
