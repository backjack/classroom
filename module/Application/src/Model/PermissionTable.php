<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/27/2017
 * Time: 10:39 AM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class PermissionTable extends BaseTable {
    protected $tableName='permission';
    protected $primary='permission_id';

    public function getRecords(){
        $select = new Select($this->tableName);
        $select->join('permission_group',$this->tableName.'.permission_group_id=permission_group.permission_group_id');

        return $this->tableGateway->selectWith($select);
    }

    public function pathExists($path){

        $total = $this->tableGateway->select(['path'=>$path])->count();
        if(!empty($total)){
            return true;
        }
        else{
            return false;
        }

    }

    public function hasPermission($path){

        $rolePermissionTable = new RolePermissionTable($this->getServiceLocator());
        if(!$this->pathExists($path)){
            return true;
        }

        //get the account and retrieve its role
        $admin = $this->getAdmin();
        $roleId = $admin->role_id;

        //get this permission
        $row = $this->tableGateway->select(['path'=>$path])->current();
        $permissionId = $row->permission_id;
        if($rolePermissionTable->roleHasPermission($roleId,$permissionId)){
            return true;
        }
        else{
            return false;
        }

    }

    public function getAdmin()
    {
        $authService = $this->getAuthService();
        $identity = $authService->getIdentity();
        $email = $identity['email'];
        $accountsTable= new \Application\Model\AccountsTable($this->getServiceLocator());
        $row = $accountsTable->getAccountWithEmail($email);
        return $row;
    }


    public function getAuthService()
    {
        return $this->getServiceLocator()->get('StudentAuthService');
    }

    public function getRecordForPermission($permission){
        $row = $this->tableGateway->select(['permission'=>$permission])->current();
        return $row;
    }

    public function getGroupPermissions($groupId){
        return $this->tableGateway->select(['permission_group_id'=>$groupId]);
    }
}