<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/13/2017
 * Time: 3:50 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class StudentFieldTable extends BaseTable {

    protected $tableName = 'student_field';
    protected $primary = 'student_field_id';

    public function saveField($studentId,$fieldId,$value)
    {
        $total = $this->tableGateway->select(['student_id'=>$studentId,'registration_field_id'=>$fieldId])->count();

        if(empty($total)){
            $this->addRecord([
               'student_id'=>$studentId,
                'registration_field_id'=>$fieldId,
                'value'=>$value
            ]);
        }
        else{
            $this->tableGateway->update(['value'=>$value],['student_id'=>$studentId,'registration_field_id'=>$fieldId,]);
        }


    }

    public function getStudentFieldRecord($studentId,$fieldId){
         $select = new Select($this->tableName);
        $select->where(['student_id'=>$studentId,'student_field.registration_field_id'=>$fieldId])
            ->join('registration_field','student_field.registration_field_id=registration_field.registration_field_id',['type','name']);
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->current();

    }

    public function getStudentRecords($studentId){
        return $this->tableGateway->select(['student_id'=>$studentId]);
    }

    public function getStudentRecordsAll($studentId){
        $select = new Select($this->tableName);
        $select->where(['student_id'=>$studentId])
            ->join('registration_field','student_field.registration_field_id=registration_field.registration_field_id');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

}