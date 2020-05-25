<?php

namespace Application\Model;

use Intermatics\BaseTable;
use Intermatics\UtilityFunctions;
use Application\Model\Parents;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;



class StudentTable extends BaseTable {
	
	

	protected  $tableName = 'student';
	protected  $primary = 'student_id';
	
	public function getStudents($paginated=false,$filter=null)
	{
		$select = new Select($this->tableName); 
		
		if(isset($filter))
		{
			$filter = $this->db->escape($filter);
			//$select->where('(student.first_name LIKE \'%'.$filter.'%\' OR student.last_name LIKE \'%'.$filter.'%\' OR student.email LIKE \'%'.$filter.'%\')');
           $select->where("MATCH (student.first_name,student.last_name,student.email) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)");

        }
        else{
            $select->order('student_id desc');
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
	
	
	public function saveStudent(Student $student)
	{
			
		$fields = UtilityFunctions::getObjectProperties($student);
	
		$data =array();
		foreach ($fields as $key=>$value)
		{
			$data[$key] = $value;
		}
	
		$id = (int)$student->student_id;
		if ($id == 0) {
	
			$data['student_created'] = time();
			//add student
			$id = $this->addRecord($data);
		}
		else {
			//update student
			if ($this->getStudent($id)) {
				$this->tableGateway->update($data, array('student_id' => $id));
			} else {
				throw new \Exception('Student ID does not exist');
			}
		}
	
		return $id;
	}
	
	public function getStudent($id)
	{



		$id = (int) $id;


        $select = new Select($this->tableName);



       $select->where(array('student_id'=>$id));
        $rowset = $this->tableGateway->selectWith($select);

		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	
	public function getStudentWithEmail($email)
	{
		$rowset = $this->tableGateway->select(array('email' => $email));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $email");
		}
		return $row;
	}
	
	
	
	public function emailExists($email)
	{
		$rowset = $this->tableGateway->select(array('email'=>$email));
		$total = $rowset->count();
		if (empty($total)) {
			return false;
		}
		else {
			return true;
		}
	}

    public function activeEmailExists($email){
        $rowset = $this->tableGateway->select(array('email'=>$email,'status'=>1));
        $total = $rowset->count();
        if (empty($total)) {
            return false;
        }
        else {
            return true;
        }
    }


	public function usernameExists($username)
	{
		$rowset = $this->tableGateway->select(array('username'=>$username));
		$total = $rowset->count();
		if (empty($total)) {
			return false;
		}
		else {
			return true;
		}
	}
	
}

?>