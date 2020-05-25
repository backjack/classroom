<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/7/2017
 * Time: 1:40 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SessionToSessionCategoryTable extends BaseTable {

    protected $tableName = 'session_to_session_category';
    protected $primary = 'session_to_session_category_id';


    public function clearSessionRecords($id){
        return $this->tableGateway->delete(['session_id'=>$id]);
    }

    public function getSessionRecords($id){
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$id]);
        $select->join('session_category',$this->tableName.'.session_category_id=session_category.session_category_id',['category_name']);

        return $this->tableGateway->selectWith($select);
    }

}