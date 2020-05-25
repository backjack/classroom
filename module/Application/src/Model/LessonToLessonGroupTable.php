<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/7/2017
 * Time: 1:48 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class LessonToLessonGroupTable extends BaseTable {

    protected $tableName = 'lesson_to_lesson_group';
    protected $primary = 'lesson_to_lesson_group_id';

    public function clearLessonRecords($id){
        return $this->tableGateway->delete(['lesson_id'=>$id]);
    }

    public function getLessonRecords($id){
        $select = new Select($this->tableName);
        $select->where(['lesson_id'=>$id]);
        $select->join('lesson_group',$this->tableName.'.lesson_group_id=lesson_group.lesson_group_id',['group_name']);

        return $this->tableGateway->selectWith($select);
    }

}