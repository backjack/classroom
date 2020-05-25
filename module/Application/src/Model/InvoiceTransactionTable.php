<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/11/2018
 * Time: 4:48 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class InvoiceTransactionTable extends BaseTable {

    protected $tableName = 'invoice_transaction';
    protected $primary = 'invoice_transaction_id';

    public function transactionExists($tid){
        $total = $this->tableGateway->select(['invoice_transaction_id'=>$tid])->count();
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
        $select->join('invoice',$this->tableName.'.invoice_id=invoice.invoice_id')
            ->join('student','invoice.student_id=student.student_id',['first_name','last_name','email'])
            ->join('payment_method','invoice.payment_method_id=payment_method.payment_method_id',['payment_method']);

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