<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/19/2017
 * Time: 4:04 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class PaymentMethodFieldTable extends BaseTable {

    protected $tableName = 'payment_method_field';
    protected $primary = 'payment_method_field_id';


    public function getRecordsForMethod($id){
        return $this->tableGateway->select(['payment_method_id'=>$id]);
    }

    public function updateValue($value,$key,$pmid){

        $where = ['payment_method_id'=>$pmid,'key'=>$key];
        $data = ['value'=>$value];
        $total = $this->tableGateway->update($data,$where);
        return $total;

    }

    public function getCodeValues($code)
    {
        $select = new Select($this->tableName);
        $select->join('payment_method',$this->tableName.'.payment_method_id=payment_method.payment_method_id');
        $select->where(['code'=>$code]);
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }


}