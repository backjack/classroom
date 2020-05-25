<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/8/2017
 * Time: 2:46 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class DiscussionTable extends BaseTable {

    protected $tableName = 'discussion';
    protected $primary = 'discussion_id';

    public function getTotalDiscussions($replied=0){
        return $this->tableGateway->select(['replied'=>$replied])->count();
    }
    public function getPaginatedRecordsForStudent($paginated=false,$id,$sessionId=null,$lectureId=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc')
            ->where(['student_id'=>$id]);
        if(!empty($sessionId)){
            $select->where(['session_id'=>$sessionId]);
        }

        if(!empty($lectureId)){
            $select->where(['lecture_id'=>$lectureId]);
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

    public function getRecord($id){
        $select = new Select($this->tableName);
        $select->where([$this->primary=>$id]);
      //  $select->order($this->primary.' desc');
        $select->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name']);
        $row= $this->tableGateway->selectWith($select)->current();
        return $row;


    }

    public function getDiscussRecords($paginated=false,$replied=null)
    {
        if(GLOBAL_ACCESS){
            $select = new Select($this->tableName);
        }
        else{
            $select = new Select('discussion_account');
            $select->join('discussion','discussion_account.discussion_id=discussion.discussion_id',['student_id','subject','question','created_on','replied','session_id','lecture_id','admin'])
                ->where(['discussion_account.account_id'=>ADMIN_ID]);
        }

        $select->order($this->tableName.'.discussion_id desc');
        $select->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name']);

        if(isset($replied)){
            $select->where(['replied'=>$replied]);
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

}