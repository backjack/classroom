<?php

use Phinx\Migration\AbstractMigration;

class TrimPermissions extends AbstractMigration
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
        $rowset = $this->query("SELECT * FROM permission");
        foreach($rowset as $row){

            $permission = trim($row['permission']);
            $id = $row['permission_id'];
            if(!empty($id)){
                $this->execute("UPDATE permission set permission='{$permission}' where permission_id={$id} ");
            }



        }
    }
}
