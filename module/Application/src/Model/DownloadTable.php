<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 7/27/2017
 * Time: 1:17 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class DownloadTable extends BaseTable {
    protected $tableName = 'download';
    protected $primary= 'download_id';
    protected $accountId = true;


    public function getValidRecords($paginated=false)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc')
            ->where(['status'=>1]);

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

    public function getDownload($id,$studentId){
        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());
        $totalSessions = $downloadSessionTable->getTotalForDownload($id);

        if($totalSessions>0){
            $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
            $sessionList = $downloadSessionTable->getDownloadRecords($id);
            $status = false;
            foreach($sessionList as $row){
                if($studentSessionTable->enrolled($studentId,$row->session_id)){
                    $status = true;
                }
            }

            if($status){
                return $this->getRecord($id);
            }
            else{
                return false;
            }
        }
        else{
            return $this->getRecord($id);
        }


    }




}