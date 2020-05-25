<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/7/2017
 * Time: 1:32 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SessionCategoryTable extends BaseTable {

    protected $tableName='session_category';
    protected $primary='session_category_id';

    public function getLimitedRecords($limit)
    {
        $select = new Select($this->tableName);

        $select->limit($limit);
        $select->order('sort_order');


        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getActiveRecords($limit){
        $select = new Select($this->tableName);

        $select->limit($limit);
        $select->order('sort_order');
        $select->where(['status'=>1]);


        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


}