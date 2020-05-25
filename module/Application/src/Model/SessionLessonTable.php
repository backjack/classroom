<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/10/2017
 * Time: 4:15 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SessionLessonTable extends BaseTable {

    protected $tableName = 'session_lesson';
    protected $primary = 'session_lesson_id';


    public function clearSessionLessons($id){
        $this->tableGateway->delete(['session_id'=>$id]);
    }

    public function getSessionRecords($id,$type=null){
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$id])
                ->join('lesson','session_lesson.lesson_id=lesson.lesson_id',['lesson_name','lesson_type','picture','content','test_required','test_id'])
                ->order($this->tableName.'.sort_order');
        if($type){
            $select->where(['lesson_type'=>$type]);
        }
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }

    public function getSessionRecordsDateSorted($id){
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$id])
            ->join('lesson','session_lesson.lesson_id=lesson.lesson_id',['lesson_name','lesson_type','picture','content'])
            ->order($this->tableName.'.lesson_date asc');
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }


    public function getLessonDate($sessionId,$lessonId){

        $row = $this->tableGateway->select(['session_id'=>$sessionId,'lesson_id'=>$lessonId])->current();
        return $row->lesson_date;
    }

    public function getSessionLessonRecord($sessionId,$lessonId){
        $row = $this->tableGateway->select(['session_id'=>$sessionId,'lesson_id'=>$lessonId])->current();
        return $row;
    }

    public function getSessionLessonWithSortOrder($sessionId,$sortOrder){

    }

    public function lessonExists($sessionId,$lessonId){
        $total= $this->tableGateway->select(['session_id'=>$sessionId,'lesson_id'=>$lessonId])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getUpcomingLessons($days){
        $upperLimit = strtotime('tomorrow midnight') - 1;

        $timestamp = strtotime("+$days day");

        $timeLimit = mktime(23,59,0,date('n',$timestamp),date('j',$timestamp),date('Y',$timestamp));


        $select = new Select($this->tableName);
        $select->where(['lesson_date < '.$timeLimit,'lesson_date > '.$upperLimit])
            ->join('lesson','session_lesson.lesson_id=lesson.lesson_id',['lesson_name','lesson_type'])
            ->join('session',$this->tableName.'.session_id=session.session_id')
            ->order('lesson_date');
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }

    public function getStartedLessons($type='c'){
        $timestamp = time();
        $upperLimit = strtotime('yesterday midnight');

        $timeLimit = mktime(23,59,0,date('n',$timestamp),date('j',$timestamp),date('Y',$timestamp));


        $select = new Select($this->tableName);
        $select->where(['lesson_date < '.$timeLimit,'lesson_date > '.$upperLimit])
            ->join('lesson','session_lesson.lesson_id=lesson.lesson_id',['lesson_name','lesson_type'])
            ->join('session',$this->tableName.'.session_id=session.session_id')
            ->order('lesson_date');
        if($type){
            $select->where(['lesson_type'=>$type]);
        }
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }

      public function arrangeSortOrdersDateSorted($id){
    $rowset = $this->getSessionRecordsDateSorted($id);
    $count = 1;
    foreach($rowset as $row){
        $this->update(['sort_order'=>$count],$row->session_lesson_id);
        $count++;
    }
}



    public function arrangeSortOrders($id){
        $rowset = $this->getSessionRecords($id);
        $count = 1;
        foreach($rowset as $row){
            $this->update(['sort_order'=>$count],$row->session_lesson_id);
            $count++;
        }
    }

    public function getPreviousLessonInSession($sessionId,$id,$type=null){
        $row = $this->getSessionLessonRecord($sessionId,$id);
        $sortOrder = $row->sort_order;
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$sessionId])
            ->where("session_lesson.sort_order < '$sortOrder'")
            ->order('sort_order desc')
            ->join('lesson',$this->tableName.'.lesson_id=lesson.lesson_id',['lesson_name','lesson_type'])
            ->limit(1);
        if($type){
            $select->where(['lesson_type'=>$type]);
        }

        $rowset = $this->tableGateway->selectWith($select);
        if($rowset->count() ==0){
            return false;
        }
        else{
            return $rowset->current();

        }
    }

    public function getNextLessonInSession($sessionId,$id,$type=null){
        $row = $this->getSessionLessonRecord($sessionId,$id);
        $sortOrder = $row->sort_order;
        $select = new Select($this->tableName);
        $select->where(['session_id'=>$sessionId])
            ->where("session_lesson.sort_order > '$sortOrder'")
                ->order('sort_order')
                ->join('lesson',$this->tableName.'.lesson_id=lesson.lesson_id',['lesson_name','lesson_type'])
                ->limit(1);
        if($type){
            $select->where(['lesson_type'=>$type]);
        }

        $rowset = $this->tableGateway->selectWith($select);
        if($rowset->count() ==0){
            return false;
        }
        else{
            return $rowset->current();

        }

    }

}