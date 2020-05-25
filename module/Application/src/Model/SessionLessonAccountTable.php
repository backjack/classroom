<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/3/2017
 * Time: 1:01 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SessionLessonAccountTable extends BaseTable {
    protected $tableName='session_lesson_account';
    protected $primary='session_lesson_account_id';

    public function accountExists($lessonId,$sessionId,$accountId){

        $total = $this->tableGateway->select(['lesson_id'=>$lessonId,'session_id'=>$sessionId,'account_id'=>$accountId])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getTotalInstructors($lessonId,$sessionId){

        $total = $this->tableGateway->select(['lesson_id'=>$lessonId,'session_id'=>$sessionId])->count();
        return $total;
    }

    public function getInstructors($lessonId,$sessionId){
        $select = new Select($this->tableName);
        $select->where(['lesson_id'=>$lessonId,'session_id'=>$sessionId])
            ->join('accounts',$this->tableName.'.account_id=accounts.account_id',['first_name','last_name','email']);
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function clearSessionLessons($id,$lessonId){
        $this->tableGateway->delete(['session_id'=>$id,'lesson_id'=>$lessonId]);
    }


    public function getSessionRecords($id){
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$id]);
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }



}