<?php

use Phinx\Migration\AbstractMigration;

class RemoveInvalideSocialSettings extends AbstractMigration
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
        $sql = 'delete from setting where setting_id >= 20 and setting_id <= 24';
        $this->execute($sql);

        $this->execute('update setting set value=0 where setting_id=98 or setting_id=101');

    }
}
