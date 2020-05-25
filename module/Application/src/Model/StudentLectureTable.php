<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/7/2017
 * Time: 1:54 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class StudentLectureTable extends BaseTable {

    protected $tableName = 'student_lecture';
    protected $primary = 'student_lecture_id';

    public function hasLecture($studentId,$sessionId){
        $total = $this->tableGateway->select(['student_id'=>$studentId,'session_id'=>$sessionId])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getLecture($studentId,$sessionId){
        $select = new Select($this->tableName);
        $select->where(['student_id'=>$studentId,'session_id'=>$sessionId])
                ->join('lecture',$this->tableName.'.lecture_id=lecture.lecture_id',['lesson_id','sort_order']);
        $row = $this->tableGateway->selectWith($select)->current();
        return $row;
    }

    public function clearLecture($studentId,$sessionId){
        $this->tableGateway->delete(['student_id'=>$studentId,'session_id'=>$sessionId]);
    }

    public function logLecture($studentId,$sessionId,$lectureId){
        //check for this record
        $total  = $this->tableGateway->select([
            'student_id'=>$studentId,
            'session_id'=>$sessionId,
            'lecture_id'=>$lectureId
        ])->count();
        if(empty($total)){
            $this->clearLecture($studentId,$sessionId);
            $this->addRecord([
               'student_id'=>$studentId,
                'session_id'=>$sessionId,
                'lecture_id'=>$lectureId
            ]);
        }
    }

}