<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/22/2018
 * Time: 12:39 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SessionTestTable extends BaseTable {

    protected $tableName = 'session_test';
    protected $primary = 'session_test_id';

    public function getSessionRecords($id){

        $select = new Select($this->tableName);
        $select->where([$this->tableName.'.session_id'=>$id])
            ->join('test','session_test.test_id=test.test_id',['name','test_id','account_id','test_status'=>'status','minutes','passmark','allow_multiple'])
            ->join('session','session_test.session_id=session.session_id',['session_name'])
            ->order($this->tableName.'.opening_date');
     
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }

    public function getTestRecords($id){
        $select = new Select($this->tableName);
        $select->where([$this->tableName.'.test_id'=>$id])
            ->join('test','session_test.test_id=test.test_id',['name'])
            ->join('session','session_test.session_id=session.session_id',['session_name'])
            ->order($this->tableName.'.opening_date');

        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }


    public function getUpcomingTests($days){
        $upperLimit = strtotime('tomorrow midnight') - 1;

        $timestamp = strtotime("+$days day");

        $timeLimit = mktime(23,59,0,date('n',$timestamp),date('j',$timestamp),date('Y',$timestamp));


        $select = new Select($this->tableName);
        $select->where(['opening_date < '.$timeLimit,'opening_date > '.$upperLimit])
            ->where(['opening_date > 0'])
            ->join('test','session_test.test_id=test.test_id',['name'])
            ->join('session',$this->tableName.'.session_id=session.session_id',['session_name'])
            ->order('opening_date');
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }

}