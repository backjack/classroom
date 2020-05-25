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

class LessonFileTable extends BaseTable {

    protected $tableName = 'lesson_file';
    protected $primary = 'lesson_file_id';

    public function getDownloadRecords($id){

        $select = new Select($this->tableName);
        $select->where(['lesson_id'=>$id])
            ->order('lesson_file_id desc');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getTotalForDownload($id){
        $rowset = $this->tableGateway->select(['lesson_id'=>$id]);
        $count = $rowset->count();
        return $count;
    }

    public function fileExists($id,$path){
        $total = $this->tableGateway->select(['lesson_id'=>$id,'path'=>$path])->count();
        return !empty($total);
    }

}