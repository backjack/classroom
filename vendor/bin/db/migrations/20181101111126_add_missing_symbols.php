<?php

use Phinx\Migration\AbstractMigration;

class AddMissingSymbols extends AbstractMigration
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
        $rowset = $this->query('SELECT * FROM country where symbol_left IS NULL');
        foreach($rowset as $row){
            $code = $row['iso_code_2'];
            $code =  strtolower($code);

            try{

                ini_set('default_socket_timeout', 900);
                $json = file_get_contents("https://restcountries.eu/rest/v2/alpha/{$code}");
                $obj= json_decode($json);
                $symbol = $obj->currencies[0]->symbol;
                if(!empty($symbol)){
                    $this->execute("UPDATE country SET symbol_left='{$symbol}' WHERE country_id={$row['country_id']}");

                }

            }
            catch(\Exception $ex){

            }


        }

    }
}
