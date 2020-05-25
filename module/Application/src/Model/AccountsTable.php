<?php

namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AccountsTable extends BaseTable {
	
	protected $tableName = 'accounts';
	protected $primary = 'account_id';
	
	public function getAccountWithEmail($email){
        return $this->tableGateway->select(['email'=>$email])->current();
    }

    public function getAccountsForNotification(){
        return $this->tableGateway->select(['notify'=>1]);
    }

    public function getRecordsSorted()
    {
        $select = new Select($this->tableName);
        $select->order('first_name asc');
        $select->join('role',$this->tableName.'.role_id=role.role_id');
        return $this->tableGateway->selectWith($select);
    }

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->join('role',$this->tableName.'.role_id=role.role_id',['role']);
        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
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


    public function hasPermission($id,$permission){
        $user = $this->getRecord($id);
        $rolePermissiontable = new RolePermissionTable($this->getServiceLocator());
        $permissionTable = new PermissionTable($this->getServiceLocator());
        $permissionRow = $permissionTable->getRecordForPermission($permission);
        if($rolePermissiontable->roleHasPermission($user->role_id,$permissionRow->permission_id)){
        return true;
        }
        else{
            return false;
        }
    }


}

?>