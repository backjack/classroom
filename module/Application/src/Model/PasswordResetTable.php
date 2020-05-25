<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/6/2017
 * Time: 3:19 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class PasswordResetTable extends BaseTable {
    protected $tableName = 'password_reset';
    protected $primary = 'password_reset_id';


    public function addEntry($email)
    {

        $token = md5(rand(1000,30000000).time().$email);

        $data = array(
            'email'=>$email,
            'token'=>$token,
            'created_on'=>time()
        );

        $id = $this->addRecord($data);


        return $token;
    }

    public function validToken($token)
    {
        $limit = time() - 86400;
        $select = new Select($this->tableName);
        $select->where(array('token'=>$token,"created_on > $limit"));
        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        if (empty($total)) {
            return false;
        }
        else {
            $row = $rowset->current();
            return $row;
        }
    }

    public function deleteTokenRecords($token)
    {
        $where = array('token'=>$token);
        $total = $this->tableGateway->delete($where);
        return $total;
    }


    public function hasEntry($email)
    {
        $limit = time() - 86400;
        $select = new Select($this->tableName);
        $select->where(array('email'=>$email,"created_on > $limit"));
        $rowet = $this->tableGateway->selectWith($select);
        $total = $rowet->count();
        if (empty($total)) {
            return false;
        }
        else{
            return true;
        }

    }


}