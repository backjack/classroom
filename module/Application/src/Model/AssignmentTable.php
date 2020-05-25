<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/5/2017
 * Time: 11:34 AM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AssignmentTable extends BaseTable {

    protected $tableName = 'assignment';
    protected $primary = 'assignment_id';
    protected $accountId = true;


    public function getPaginatedRecords($paginated=false,$sid=null)
    {
        $select = new Select($this->tableName);
        $select->join('session',"$this->tableName.session_id=session.session_id",array('session_name'));

        if (isset($sid)) {
            $select->where(array('session.session_id'=>$sid));
        }
        $select->order('assignment_id desc');

        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
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

    public function getAssignmentForStudent($studentId){

    }

    public function getTotalAdminAssignments($id){
        $total = $this->tableGateway->select(['account_id'=>$id,'due_date > '.time()])->count();

        return $total;
    }

    public function getUpcomingAssignments($days=3){
        $upperLimit = strtotime('tomorrow midnight') - 1;

        $timestamp = strtotime("+$days day");

        $timeLimit = mktime(23,59,0,date('n',$timestamp),date('j',$timestamp),date('Y',$timestamp));


        $select = new Select($this->tableName);
        $select->where(['due_date < '.$timeLimit,'due_date > '.$upperLimit,'opening_date < '.time()])
            ->join('session',$this->tableName.'.session_id=session.session_id')
            ->order('due_date');
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }


    public function getSessionLessonAssignments($sessionId,$lessonId){

        $rowset = $this->tableGateway->select(['session_id'=>$sessionId,'lesson_id'=>$lessonId]);
        return $rowset;

    }

}