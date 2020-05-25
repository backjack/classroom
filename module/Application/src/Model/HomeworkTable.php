<?php

namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class HomeworkTable extends BaseTable {

	protected $tableName = 'homework';
	protected $primary = 'homework_id';
    protected $accountId = true;
	
	
	public function getPaginatedRecords($paginated=false,$sid=null)
	{
		$select = new Select($this->tableName);
		$select->join('session',"$this->tableName.session_id=session.session_id",array('session_name'))
            ->join('lesson',$this->tableName.'.lesson_id=lesson.lesson_id',['lesson_name']);
	
		if (isset($sid)) {
			$select->where(array('session.session_id'=>$sid));
		}
		$select->order('homework_id desc');

        if(!GLOBAL_ACCESS){
            $select->where(['homework.account_id'=>ADMIN_ID]);
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
	
	public function getHomework($id)
	{
		$select = new Select($this->tableName);
		$select->join('session',"$this->tableName.session_id=session.session_id",array('session_name'));

        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }

		$select->where(array($this->primary=>$id));
		 
	
		$resultSet = $this->tableGateway->selectWith($select);
		return $resultSet->current();
	}
	
	
	public function getTotalForCategory($cid)
	{
		$rowset = $this->tableGateway->select(array('session_id'=>$cid));
        if(!GLOBAL_ACCESS){
            $rowset = $this->tableGateway->select(array('session_id'=>$cid,'account_id'=>ADMIN_ID));
        }
		$total = $rowset->count();
		return $total;
	}
	
	
	
}

?>