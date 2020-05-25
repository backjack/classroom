<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/5/2017
 * Time: 11:33 AM
 */

namespace Application\Model;


use Application\Entity\Session;
use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SessionInstructorTable extends BaseTable {

    protected $tableName = 'session_instructor';
    protected $primary = 'session_instructor_id';

    public function clearSessionRecords($id){
        return $this->tableGateway->delete(['session_id'=>$id]);
    }

    public function getSessionRecords($id){
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$id]);
        $select->join('accounts',$this->tableName.'.account_id=accounts.account_id',['first_name','last_name','email','picture','account_description']);

        return $this->tableGateway->selectWith($select);
    }

    public function getAccountRecords($accountId){
        $select = new Select($this->tableName);
        $select->where([$this->tableName.'.account_id'=>$accountId])
                ->join('session',$this->tableName.'.session_id=session.session_id',['session_name','session_end_date','session_date','session_status','session_type'])
                ->limit(3000);
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function isInstructor($sessionId,$accountId){
        if(Session::where('session_id',$sessionId)->where('account_id',$accountId)->count() > 0){
            return true;
        }
        else{
            return false;
        }
    }

}