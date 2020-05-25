<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/15/2018
 * Time: 5:46 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SmsGatewayFieldTable extends BaseTable{

    protected $tableName= 'sms_gateway_field';
    protected $primary = 'sms_gateway_field_id';


    public function saveField($gatewayId,$key,$value){
        $total= $this->tableGateway->update(['value'=>$value],['key'=>$key,'sms_gateway_id'=>$gatewayId,]);
        return $total;
    }

    public function getGatewayRecords($gatewayId){
        $select = new Select($this->tableName);
        $select->where(['sms_gateway_id'=>$gatewayId]) ;
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    

    public function getField($gatewayId,$setting){

        $row = $this->tableGateway->select(['key'=>$setting,'sms_gateway_id'=>$gatewayId])->current();

        $val = $row->value;


        return $val;
    }

}