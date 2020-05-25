<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/27/2017
 * Time: 10:42 AM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class RolePermissionTable extends BaseTable {

    protected $tableName = 'role_permission';
    protected $primary = 'role_permission_id';

    public function getPermissionsForRole($id)
    {
        $select = new Select($this->tableName);
        $select->where(['role_id'=>$id])
                ->join('permission',$this->tableName.'.permission_id=permission.permission_id')
                ->join('permission_group','permission.permission_group_id=permission_group.permission_group_id');


        return $this->tableGateway->selectWith($select);
    }

    public function deletePermissionsForRole($id){
        return $this->tableGateway->delete(['role_id'=>$id]);
    }

    public function roleHasPermission($roleId,$permissionId){
        $total = $this->tableGateway->select(['role_id'=>$roleId,'permission_id'=>$permissionId])->count();
        if(empty($total))
        {
            return false;
        }
        else{
            return true;
        }
    }

    public function roleHasPermissionName($roleId,$permission){

    }


}