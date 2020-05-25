<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 1:37 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StudentTestTable extends BaseTable {

    protected $tableName = 'student_test';
    protected $primary = 'student_test_id';

    public function getAverageScore($id,$startDate=null,$endDate=null){
        $select= new Select($this->tableName);
        $select->where(['test_id'=>$id])
                ->columns(['total'=>new Expression('avg(score)')]);
        if($startDate){
            $select->where($this->tableName.'.created_on >= '.$startDate);
        }

        if($endDate){
            $select->where($this->tableName.'.created_on <= '.$endDate);
        }

        $row = $this->tableGateway->selectWith($select)->current();
        return floor($row->total);
    }

    public function hasTest($id,$studentId){
        $total = $this->tableGateway->select(['test_id'=>$id,'student_id'=>$studentId])->count();
        if(empty($total))
        {
            return false;
        }
        else{
            return true;
        }
    }

    public function getTotalForTest($id,$startDate=null,$endDate=null)
    {
        $select = new Select($this->tableName);
        $select->where(['test_id'=>$id]);
        if($startDate){
            $select->where($this->tableName.'.created_on >= '.$startDate);
        }

        if($endDate){
            $select->where($this->tableName.'.created_on <= '.$endDate);
        }

        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function getTotalPassedForTest($id,$score,$startDate=null,$endDate=null)
    {
       // $total = $this->tableGateway->select(['test_id'=>$id,'score >= '.$score])->count();
        $select = new Select($this->tableName);
        $select->where(['test_id'=>$id,'score >= '.$score]);
        if($startDate){
            $select->where($this->tableName.'.created_on >= '.$startDate);
        }

        if($endDate){
            $select->where($this->tableName.'.created_on <= '.$endDate);
        }

        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;

    }

    public function getTotalFailedForTest($id,$score)
    {
        $total = $this->tableGateway->select(['test_id'=>$id,'score < '.$score])->count();
        return $total;
    }

    public function getTotalPassed($testId,$passmark,$startDate=null,$endDate=null){
       // $total = $this->tableGateway->select(['test_id'=>$testId,'score >= '.$passmark])->count();

        $select = new Select($this->tableName);
        $select->where(['test_id'=>$testId,'score >= '.$passmark]);
        if($startDate){
            $select->where($this->tableName.'.created_on >= '.$startDate);
        }

        if($endDate){
            $select->where($this->tableName.'.created_on <= '.$endDate);
        }

        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();

        return $total;
    }

    public function passedTest($studentId,$testId){
        $testTable = new TestTable($this->getServiceLocator());
        $testRow = $testTable->getRecord($testId);
        $total = $this->tableGateway->select(['test_id'=>$testId,'score >= '.$testRow->passmark,'student_id'=>$studentId])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getPaginatedRecords($paginated=false,$id=null,$filter=null,$startDate=null,$endDate=null)
    {
        $select = new Select($this->tableName);
        $select->order('score desc')
                ->where([$this->tableName.'.test_id'=>$id])
                ->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name','email'])
                ->join('test',$this->tableName.'.test_id=test.test_id',['passmark']);

        if($startDate){
            $select->where($this->tableName.'.created_on >= '.$startDate);
        }

        if($endDate){
            $select->where($this->tableName.'.created_on <= '.$endDate);
        }

        if(isset($filter))
        {
            $filter = $this->db->escape($filter);
            $select->where('(student.first_name LIKE \'%'.$filter.'%\' OR student.last_name LIKE \'%'.$filter.'%\' OR student.email LIKE \'%'.$filter.'%\')');
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


    public function getPassedPaginatedRecords($paginated=false,$id=null,$score,$startDate=null,$endDate=null)
    {
        $select = new Select($this->tableName);
        $select->order('score desc')
            ->where([$this->tableName.'.test_id'=>$id,'score >= '.$score])
            ->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name'])
            ->join('test',$this->tableName.'.test_id=test.test_id',['passmark']);

        if($startDate){
            $select->where($this->tableName.'.created_on >= '.$startDate);
        }

        if($endDate){
            $select->where($this->tableName.'.created_on <= '.$endDate);
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


    public function getFailPaginatedRecords($paginated=false,$id=null,$score,$startDate=null,$endDate=null)
    {
        $select = new Select($this->tableName);
        $select->order('score desc')
            ->where([$this->tableName.'.test_id'=>$id,'score < '.$score])
            ->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name'])
            ->join('test',$this->tableName.'.test_id=test.test_id',['passmark']);

        if($startDate){
            $select->where($this->tableName.'.created_on >= '.$startDate);
        }

        if($endDate){
            $select->where($this->tableName.'.created_on <= '.$endDate);
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


    public function getStudentRecord($studentId,$testId){
        $select = new Select($this->tableName);
        $select->where([$this->tableName.'.student_id'=>$studentId,$this->tableName.'.test_id'=>$testId])
        ->order('test_id desc')
        ->join('test',$this->tableName.'.test_id=test.test_id',['name'])
        ->join('student',$this->tableName.'.student_id=student.student_id');
        return $this->tableGateway->selectWith($select)->current();
    }
}