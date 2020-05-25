<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/12/2017
 * Time: 4:31 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class RegistrationFieldTable extends BaseTable {

    protected $tableName = 'registration_field';
    protected $primary = 'registration_field_id';

    public function getActiveFields()
    {
        $select = new Select($this->tableName);
        $select->where(['status'=>1])
                ->order('sort_order');
        return $this->tableGateway->selectWith($select);
    }

    public function getAllFields(){
        $select = new Select($this->tableName);
        $select->order('sort_order');
        return $this->tableGateway->selectWith($select);
    }


}