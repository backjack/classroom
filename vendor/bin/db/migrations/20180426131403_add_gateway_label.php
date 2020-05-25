<?php

use Phinx\Migration\AbstractMigration;

class AddGatewayLabel extends AbstractMigration
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



        $table = $this->table('payment_method');
        $table->addColumn('method_label','string',['null'=>'true'])
            ->update();

        //get all and update
        $rowset = $this->query("SELECT * FROM payment_method");
        foreach($rowset as $row){
                $name = $row['payment_method'];
                $id = $row['payment_method_id'];
            $this->execute("update payment_method set method_label='$name' where payment_method_id=$id");
        }

        $this->execute("update payment_method set payment_method='Payu.co.za' where payment_method_id=6");

    }
}
