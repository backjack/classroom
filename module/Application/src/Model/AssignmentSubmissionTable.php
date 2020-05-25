<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/5/2017
 * Time: 11:34 AM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AssignmentSubmissionTable extends BaseTable {

    protected $tableName = 'assignment_submission';
    protected $primary = 'assignment_submission_id';



    public function getAssignmentPaginatedRecords($paginated=false,$id,$submitted=1)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where([$this->tableName.'.assignment_id'=>$id])
            ->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name','email'])
            ->join('assignment',$this->tableName.'.assignment_id=assignment.assignment_id',['title','instruction','passmark','session_id','due_date'])
            ->join('session','assignment.session_id=session.session_id',['session_name']);

        if($submitted){
            $select->where(['submitted'=>$submitted]);
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


    public function getTotalForAssignment($id){
        $total= $this->tableGateway->select(['assignment_id'=>$id])->count();
        return $total;
    }

    public function getTotalSubmittedForAssignment($id){
        $total= $this->tableGateway->select(['assignment_id'=>$id,'submitted'=>1])->count();
        return $total;
    }

    public function getAverageScore($id){
        $select= new Select($this->tableName);
        $select->where(['assignment_id'=>$id,'submitted'=>1])
            ->columns(['total'=>new Expression('avg(grade)')]);
        $row = $this->tableGateway->selectWith($select)->current();
        return floor($row->total);
    }


    public function getTotalPassedForAssignment($id,$grade)
    {
        $total = $this->tableGateway->select(['assignment_id'=>$id,'grade >= '.$grade])->count();
        return $total;
    }

    public function getTotalFailedForAssignment($id,$grade)
    {
        $total = $this->tableGateway->select(['assignment_id'=>$id,'grade < '.$grade])->count();
        return $total;
    }

    public function getTotalPassed($id,$passmark){
        $total = $this->tableGateway->select(['assignment_id'=>$id,'grade >= '.$passmark])->count();
        return $total;
    }

    public function passedAssignment($studentId,$testId){
        $testTable = new AssignmentTable($this->getServiceLocator());
        $testRow = $testTable->getRecord($testId);
        $total = $this->tableGateway->select(['assignment_id'=>$testId,'grade >= '.$testRow->passmark,'student_id'=>$studentId])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function hasSubmission($studentId,$assignmentId){
        $total = $this->tableGateway->select(['student_id'=>$studentId,'assignment_id'=>$assignmentId])->count();
        return $total;
    }



    public function getStudentPaginatedRecords($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(['student_id'=>$id])
            ->join('assignment',$this->tableName.'.assignment_id=assignment.assignment_id',['title','instruction','passmark','session_id','due_date'])
            ->join('session','assignment.session_id=session.session_id',['session_name']);
        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getSubmission($id){
        $select = new Select($this->tableName);
        $select->where([$this->primary=>$id])
            ->join('assignment',$this->tableName.'.assignment_id=assignment.assignment_id',['title','due_date','instruction','assignment_type','session_id','passmark'])
            ->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name','email','mobile_number']);
        $row = $this->tableGateway->selectWith($select);
        return $row->current();
    }

    public function getAssignment($assignmentId,$studentId){
        $row = $this->tableGateway->select(['assignment_id'=>$assignmentId,'student_id'=>$studentId])->current();
        return $row;
    }


    public function getPassedPaginatedRecords($paginated=false,$id=null,$score)
    {
        $select = new Select($this->tableName);
        $select->order('grade desc')
            ->where([$this->tableName.'.assignment_id'=>$id,'grade >= '.$score])
            ->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name','email'])
            ->join('assignment',$this->tableName.'.assignment_id=assignment.assignment_id',['passmark']);


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getFailPaginatedRecords($paginated=false,$id=null,$score)
    {
        $select = new Select($this->tableName);
        $select->order('grade desc')
            ->where([$this->tableName.'.assignment_id'=>$id,'grade < '.$score])
            ->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name','email'])
            ->join('assignment',$this->tableName.'.assignment_id=assignment.assignment_id',['passmark']);


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