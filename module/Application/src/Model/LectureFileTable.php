<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/7/2017
 * Time: 1:55 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class LectureFileTable extends BaseTable {

    protected $tableName = 'lecture_file';
    protected $primary = 'lecture_file_id';
/*
    public function getRecord($id){
        $select = new Select($this->tableName);
        $select->where(['lecture_file_id'=>$id])
                ->join('lecture',$this->tableName.'.lecture_id=lecture.lecture_id',['lecture_title','lesson_id','lecture_id']);
        $row = $this->tableGateway->selectWith($select)->current();
        return $row;


    }
*/

    public function getDownloadRecords($id){

        $select = new Select($this->tableName);
        $select->where(['lecture_id'=>$id])
            ->order('lecture_file_id desc');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getTotalForDownload($id){
        $rowset = $this->tableGateway->select(['lecture_id'=>$id]);
        $count = $rowset->count();
        return $count;
    }

    public function fileExists($id,$path){
        $total = $this->tableGateway->select(['lecture_id'=>$id,'path'=>$path])->count();
        return !empty($total);
    }


}