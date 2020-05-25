<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2017
 * Time: 3:43 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class CertificateTable extends BaseTable {

    protected $tableName = 'certificate';
    protected $primary = 'certificate_id';
    protected $accountId = true;


    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc')
                ->join('session',$this->tableName.'.session_id=session.session_id',['session_name']);

        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }

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