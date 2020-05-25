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

class DownloadSessionTable extends BaseTable {

    protected $tableName = 'download_session';
    protected $primary = 'download_session_id';

    public function getDownloadRecords($id){

        $select = new Select($this->tableName);
        $select->where(['download_id'=>$id]);
        $select->join('session',$this->tableName.'.session_id=session.session_id',['session_name']);
        $select->order('download_session_id desc');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getTotalForDownload($id){
        $rowset = $this->tableGateway->select(['download_id'=>$id]);
        $count = $rowset->count();
        return $count;
    }

    public function sessionExists($id,$sessionId){
        $total = $this->tableGateway->select(['download_id'=>$id,'session_id'=>$sessionId])->count();
        return !empty($total);
    }

    public function getSessionRecords($id){
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$id,'download.status'=>1])
            ->join('download',$this->tableName.'.download_id=download.download_id',['download_name']);
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }
}