<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 1:37 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class StudentTestOptionTable extends BaseTable {
    protected $tableName = 'student_test_option';
    protected $primary = 'student_test_option_id';

    public function getTestRecords($id)
    {
        $select = new Select($this->tableName);
        $select->where(['student_test_id'=>$id])
                ->join('test_option',$this->tableName.'.test_option_id=test_option.test_option_id')
                ->join('test_question','test_option.test_question_id=test_question.test_question_id');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

}