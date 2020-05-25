<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 7/27/2017
 * Time: 1:18 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class DownloadFileTable extends BaseTable {

    protected $tableName = 'download_file';
    protected $primary = 'download_file_id';

    public function getDownloadRecords($id){

        $select = new Select($this->tableName);
        $select->where(['download_id'=>$id])
            ->order('download_file_id desc');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getTotalForDownload($id){
        $rowset = $this->tableGateway->select(['download_id'=>$id]);
        $count = $rowset->count();
        return $count;
    }

    public function fileExists($id,$path){
        $total = $this->tableGateway->select(['download_id'=>$id,'path'=>$path])->count();
        return !empty($total);
    }

    public function getFile($id,$studentId){
        $downloadTable = new DownloadTable($this->getServiceLocator());
        $row = $this->getRecord($id);

        $downloadRow = $downloadTable->getDownload($row->download_id,$studentId);
        if($downloadRow){
            return $row;
        }
        else{
            exit('You do not have permission to access this file!');
        }
    }

    public function getFiles($downloadId,$studentId){
        $downloadTable = new DownloadTable($this->getServiceLocator());
        $downloadRow = $downloadTable->getDownload($downloadId,$studentId);

        if($downloadRow){
            return $this->getDownloadRecords($downloadId);
        }
        else{
            exit('You do not have permission to access this download!');
        }
    }
}