<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/18/2017
 * Time: 4:03 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class CountryTable extends BaseTable {

    protected $tableName = 'country';
    protected $primary = 'country_id';

    public function getRecords(){
        $select = new Select($this->tableName);
        $select->order('name');
        return $this->tableGateway->selectWith($select);
    }

}