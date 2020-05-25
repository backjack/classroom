<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 1:34 PM
 */

namespace Application\Model;


use Application\Entity\Test;
use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TestTable extends BaseTable{

    protected $tableName = 'test';
    protected $primary = 'test_id';
    protected $accountId = true;


    public function getPaginatedRecords($paginated=false,$id=null,$filter=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');

        if($this->accountId && !GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }

        if(isset($filter))
        {
            $filter = $this->db->escape($filter);
            $select->where('(test.name LIKE \'%'.$filter.'%\')');
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


    public function getActivePaginatedRecords($paginated=false)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc')
                ->where(['status'=>1]);

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

    public function getLimitedRecords($limit){
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc')
            ->limit($limit);
        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }
        return $this->tableGateway->selectWith($select);
    }

    public function getStudentTestRecords($studentId,$testId){
        $today = time();
        $select1 = new Select('student_session');
        $select1->join('session_test','student_session.session_id=session_test.session_id',array('opening_date','closing_date'))
            ->join('test','session_test.test_id=test.test_id',['test_id','name','status','minutes','allow_multiple','passmark','private'])
            ->where(['student_session.student_id'=>$studentId])
            ->where(['test.status'=>'1'])
            ->where(['test.test_id'=>$testId])
            ->columns(['session_id'])
            ->order('test.created_on desc');

        $rowset = $this->tableGateway->selectWith($select1);
        $rowset->buffer();
        return $rowset;
    }

    public function getStudentRecords($studentId){

        $today = time();
        $select1 = new Select('student_session');
        $select1->join('session_test','student_session.session_id=session_test.session_id',array())
                ->join('test','session_test.test_id=test.test_id',['test_id','name','status','minutes','allow_multiple','passmark','private','show_result'])
                ->where(['student_session.student_id'=>$studentId])
                ->where(['test.status'=>'1'])
                ->where("session_test.opening_date < $today OR session_test.opening_date=0")
                ->where("session_test.closing_date > $today OR session_test.closing_date=0")
                ->columns([])
                ->group('session_test.test_id')
            ->order('test.created_on desc');

        $select2 = new Select($this->tableName);
        $select2->where(['private'=>0])
               ->where(['test.status'=>'1'])
                ->columns(['test_id','name','status','minutes','allow_multiple','passmark','private','show_result']);

        $select1->combine($select2);

      //  $sql = $select1->getSqlString();
     //   exit($sql);
        $paginatorAdapter = new DbSelect($select1,$this->tableGateway->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;



    }

    public function getStudentTotalRecords($studentId){

        $today = time();
        $select1 = new Select('student_session');
        $select1->join('session_test','student_session.session_id=session_test.session_id',array())
            ->join('test','session_test.test_id=test.test_id',['test_id','name','status','minutes','allow_multiple','passmark','private','show_result'])
            ->where(['student_session.student_id'=>$studentId])
            ->where(['test.status'=>'1'])
            ->where("session_test.opening_date < $today OR session_test.opening_date=0")
            ->where("session_test.closing_date > $today OR session_test.closing_date=0")
            ->columns([])
            ->group('session_test.test_id')
            ->order('test.created_on desc');

        $select2 = new Select($this->tableName);
        $select2->where(['private'=>0])
            ->where(['test.status'=>'1'])
            ->columns(['test_id','name','status','minutes','allow_multiple','passmark','private','show_result']);

        $select1->combine($select2);

        //  $sql = $select1->getSqlString();
        //   exit($sql);

        $rowset = $this->tableGateway->selectWith($select1);
        return $rowset->count();



    }


    public function getLastSortOrder($testId){
        $row = Test::where('test_id',$testId)->orderBy('sort_order','desc')->first();
        return $row->sort_order;
    }
}