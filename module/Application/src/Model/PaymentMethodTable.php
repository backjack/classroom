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
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PaymentMethodTable extends BaseTable {

    protected $tableName = 'payment_method';
    protected $primary = 'payment_method_id';

    public function getMethodWithCode($code)
    {
        $row = $this->tableGateway->select(['code'=>trim($code)])->current();
        return $row;
    }

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
       $select->order('payment_method');

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getInstalledMethods(){
        $select = new Select($this->tableName);
        $select->where(['status'=>1]);
        $select->order('sort_order');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getMethodsForCurrency($currencyId){
        $select = new Select($this->tableName);
        $select->where(['is_global'=>1,'status'=>1])
            ->order('sort_order')
        ->columns(['payment_method','status','payment_method_id','method_label']);

        //get for currency
        $select1 = new Select('payment_method_currency');
        $select1->join('payment_method','payment_method_currency.payment_method_id=payment_method.payment_method_id',['payment_method','status','payment_method_id','method_label']);
        $select1->columns([]);
        $select1->order('sort_order');
        $select1->where(['payment_method.status'=>1]);
        $select1->where(['currency_id'=>$currencyId]);


        $select->combine($select1);

        return $this->tableGateway->selectWith($select);
    }
}