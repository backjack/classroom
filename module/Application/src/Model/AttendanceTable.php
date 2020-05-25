<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/7/2016
 * Time: 3:18 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AttendanceTable extends BaseTable {

    protected $tableName = 'attendance';
    protected $primary = 'attendance_id';


    public function getTotalForStudent($id){
        $select = new Select($this->tableName);
        $select->where(array('student_id'=>$id));
        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function getTotalForStudentInSession($id,$sessionId){
        $select = new Select($this->tableName);
        $select->where(array('student_id'=>$id,'session_id'=>$sessionId));
        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }


    public function getTotalDistinctForStudent($id){

        $select = new Select($this->tableName);
        $select->columns(array(new Expression('DISTINCT(lesson_id) as id')));
        $select->where(array('student_id'=>$id));


        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function getTotalDistinctForStudentInSession($id,$sessionId){

        $select = new Select($this->tableName);
        $select->columns(array(new Expression('DISTINCT(lesson_id) as id')));
        $select->where(array('student_id'=>$id,'session_id'=>$sessionId));


        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function getTotalStudentsForSession($sessionId){

        $select = new Select($this->tableName);
        $select->columns(array(new Expression('DISTINCT(student_id) as id')));
        $select->where(array('session_id'=>$sessionId));


        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }


    public function getStudentSessionReportRecords($sessionId){
        $select = new Select($this->tableName);
        $select->columns(array(new Expression('DISTINCT(student_id) as id')));
        $select->where(array('session_id'=>$sessionId));

        $select2 = new Select('student_session');
        $select2->columns(array(new Expression('DISTINCT(student_id) as id')));
        $select2->where(array('session_id'=>$sessionId));

        $select->combine($select2);

        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }

    public function getTotalStudentsForSessionAndLesson($sessionId,$lesssonId){

        $select = new Select($this->tableName);
        $select->columns(array(new Expression('DISTINCT(student_id) as id')));
        $select->where(array('session_id'=>$sessionId,'lesson_id'=>$lesssonId));


        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function setAttendance($data){

        if(empty($data['attendance_date'])){
            $data['attendance_date'] = time();
        }

        $select = new Select($this->tableName);
        $select->where(array('student_id'=>$data['student_id'],'session_id'=>$data['session_id'],'lesson_id'=>$data['lesson_id']));
        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();

        if(empty($total)){
            $this->addRecord($data);
            return true;
        }
        else{
            return false;
        }
    }

    public function getStudentRecords($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(array('student_id'=>$id))
                ->join('lesson','attendance.lesson_id=lesson.lesson_id',array('lesson_name','lesson_type'))
                ->join('session','attendance.session_id=session.session_id',array('session_name','session_type')) ;


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getStudentRecordsNoSession($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(array('student_id'=>$id))
            ->join('lesson','attendance.lesson_id=lesson.lesson_id',array('lesson_name'))
            ->join('lesson_category','lesson.lesson_category_id=lesson_category.lesson_category_id');


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getGroupedSessionRecords($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(array('attendance.session_id'=>$id))
            ->join('lesson','attendance.lesson_id=lesson.lesson_id',array('lesson_name'))
            ->join('session','attendance.session_id=session.session_id',array('session_name'))
            ->join('student','attendance.student_id=student.student_id')
        ->group('attendance.student_id');


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getSessionRecords($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(array('attendance.session_id'=>$id))
            ->join('lesson','attendance.lesson_id=lesson.lesson_id',array('lesson_name'))
            ->join('session','attendance.session_id=session.session_id',array('session_name'))
            ->join('student','attendance.student_id=student.student_id');


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function lessonExists($lessonId,$sessionId){

            $rowset = $this->tableGateway->select(array('session_id'=>$sessionId,'lesson_id'=>$lessonId));
            $total = $rowset->count();
            if(empty($total)){
                return false;
            }
            else{
                return true;
             }

    }

    public function hasAttendance($studentId,$lessonId,$sessionId){

        $select = new Select($this->tableName);
        $select->where(array('session_id'=>$sessionId,'lesson_id'=>$lessonId,'student_id'=>$studentId));
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

    public function hasAttendanceAll($studentId,$lessonId){

        $select = new Select($this->tableName);
        $select->where(array('lesson_id'=>$lessonId,'student_id'=>$studentId));
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

    public function setDate($sessionId,$lessonId,$date){
        $where = array(
            'lesson_id'=>$lessonId,
            'session_id'=>$sessionId
        );
        $data = array('attendance_date'=>$date);
        $total = $this->tableGateway->update($data,$where);
        return $total;
    }


    public function getStudentLessonDate($studentId,$lessonId)
    {
        $where = array(
            'lesson_id' => $lessonId,
            'student_id' => $studentId
        );

        $total = $this->tableGateway->select($where)->count();
        if (!empty($total)) {

        $row = $this->tableGateway->select($where)->current();
        $date = showDate('d/M/Y', $row->attendance_date);
        }
        else{
            $date = '';
        }
        return $date;
    }

    public function getStudentLessonDateInSession($studentId,$lessonId,$sessionId)
    {
        $where = array(
            'lesson_id' => $lessonId,
            'student_id' => $studentId,
            'session_id' => $sessionId
        );

        $total = $this->tableGateway->select($where)->count();
        if (!empty($total)) {

            $row = $this->tableGateway->select($where)->current();
            $date = showDate('d/M/Y', $row->attendance_date);
        }
        else{
            $date = '';
        }
        return $date;
    }

    public function hasClasses($studentId,$classes){

        $status = true;
        foreach($classes as $id){
            if(!$this->hasAttendanceAll($studentId,$id)){
                $status = false;
            }
        }

        return $status;
    }

    public function hasClassesInSession($studentId,$sessionId,$classes){

        $status = true;
        foreach($classes as $id){
            if(!$this->hasAttendance($studentId,$id,$sessionId)){
                $status = false;
            }
        }

        return $status;
    }

    public function getAttendedRecords($studentId,$sessionId){

        $select = new Select($this->tableName);
        $select->where(['student_id'=>$studentId,'session_id'=>$sessionId])
            ->join('lesson',$this->tableName.'.lesson_id=lesson.lesson_id',['lesson_name']);
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }


}