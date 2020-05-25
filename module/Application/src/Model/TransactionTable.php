<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/24/2017
 * Time: 2:21 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TransactionTable extends BaseTable {

    protected $tableName = 'transaction';
    protected $primary = 'transaction_id';

    public function transactionExists($tid){
        $total = $this->tableGateway->select(['transaction_id'=>$tid])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name','email'])
                ->join('session',$this->tableName.'.session_id=session.session_id',['session_name'])
                ->join('payment_method',$this->tableName.'.payment_method_id=payment_method.payment_method_id',['payment_method']);

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


}