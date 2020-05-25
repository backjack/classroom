<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/28/2017
 * Time: 12:57 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class StudentSessionLogTable extends BaseTable {

    protected $tableName ='student_session_log';
    protected $primary ='student_session_log_id';

    public function getLogRecord($id){

    }

    public function getStudentTotal($id,$sessionId){
        $total = $this->tableGateway->select(['student_id'=>$id,'session_id'=>$sessionId])->count();
        return $total;
    }

    public function getStudentRecords($id,$sessionId){
        $select = new Select($this->tableName);
        $select->where(['student_id'=>$id,$this->tableName.'.session_id'=>$sessionId])
                ->join('lesson',$this->tableName.'.lesson_id=lesson.lesson_id',['lesson_name','lesson_sort_order'=>'sort_order'])
                ->join('lecture',$this->tableName.'.lecture_id=lecture.lecture_id',['lecture_title','lecture_sort_order'=>'sort_order']);
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }

    public function getLastLesson($studentId,$sessionId){

        $select = new Select($this->tableName);
        $select->where(['student_id'=>$studentId,$this->tableName.'.session_id'=>$sessionId])
            ->join('lesson',$this->tableName.'.lesson_id=lesson.lesson_id',['lesson_name','lesson_sort_order'=>'sort_order'])
            ->order('lesson.sort_order desc')
            ->group($this->tableName.'.lesson_id');

        $row =  $this->tableGateway->selectWith($select)->current();
        return $row;
    }

    public function hasAttendance($studentId,$sessionId,$lecture_id){

        $select = new Select($this->tableName);
        $select->where(array('session_id'=>$sessionId,'student_id'=>$studentId,'lecture_id'=>$lecture_id));
        $select->columns(array('num' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $row = $this->tableGateway->selectWith($select);

        /*
        return false;
        $rowset = $this->tableGateway->select(array('session_id'=>$sessionId,'lesson_id'=>$lessonId,'student_id'=>$studentId));
        $total = $rowset->count();
        */
        $total = $row->current()->num;
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getAttendance($studentId,$sessionId,$lecture_id){

        $select = new Select($this->tableName);
        $select->where(array('session_id'=>$sessionId,'student_id'=>$studentId,'lecture_id'=>$lecture_id));

        $row = $this->tableGateway->selectWith($select)->current();
        return $row;

    }


    public function setAttendance($data){

        if(empty($data['log_date'])){
            $data['log_date'] = time();
        }

        $select = new Select($this->tableName);
        $select->where(array('student_id'=>$data['student_id'],'session_id'=>$data['session_id'],'lesson_id'=>$data['lesson_id']));
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        $total = $rowset->count();

        if(empty($total)){
            $id = $this->addRecord($data);
            return $id;
        }
        else{
            return $rowset->current()->student_session_log_id;
        }
    }

}