<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/29/2017
 * Time: 12:46 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class StudentRegistrationTable extends BaseTable {

    protected $tableName = 'student_registration';
    protected $primary  = 'student_registration_id';

    public function getCodeRecord($code){
        $row = $this->tableGateway->select(['code'=>$code])->current();
        return $row;
    }

    public function deleteCodeRecord($code){
        $total = $this->tableGateway->delete(['code'=>$code]);
    }

}