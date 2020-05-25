<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/7/2017
 * Time: 1:49 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LectureTable extends BaseTable {

    protected $tableName = 'lecture';
    protected $primary = 'lecture_id';


    public function getRecord($id){
        $select = new Select($this->tableName);
        $select->join('lesson',$this->tableName.'.lesson_id=lesson.lesson_id',['lesson_name','lesson_type'])
            ->where(['lecture_id'=>$id]);

        $row = $this->tableGateway->selectWith($select)->current();

        return $row;
    }

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order('sort_order');
        $select->where(['lesson_id'=>$id]);

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getLectureRecords($id){
        $select = new Select($this->tableName);
        $select->where(['lesson_id'=>$id]);
        $select->order('lecture_title asc');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getTotalLectures($id){
        $total = $this->tableGateway->select(['lesson_id'=>$id])->count();
        return $total;
    }

    public function getRecordsOrdered($id){
        $select = new Select($this->tableName);
        $select->where(['lesson_id'=>$id]);
        $select->order('sort_order');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getNextSortOrder($id){
        $select = new Select($this->tableName);
        $select->order('sort_order desc');
        $select->limit(1);
        $select->where(['lesson_id'=>$id]);
        $row = $this->tableGateway->selectWith($select)->current();
        if($row){
            $sortOrder = $row->sort_order;
            $sortOrder++;
        }
        else{
            $sortOrder =1;
        }
        return $sortOrder;
    }


    public function arrangeSortOrders($id){
        $rowset = $this->getRecordsOrdered($id);
        $count = 1;
        foreach($rowset as $row){
            $this->update(['sort_order'=>$count],$row->lecture_id);
            $count++;
        }
    }


    public function getPreviousLecture($id){
        $row = $this->getRecord($id);
        $sortOrder = $row->sort_order;
        $select = new Select($this->tableName);
        $select->where(['lesson_id'=>$row->lesson_id,'sort_order < '.$sortOrder])
            ->order('sort_order desc')
            ->limit(1);

        $rowset = $this->tableGateway->selectWith($select);
        if($rowset->count() ==0){
            return false;
        }
        else{
            return $rowset->current();

        }
    }

    public function getNextLecture($id){
        $row = $this->getRecord($id);
        $sortOrder = $row->sort_order;
        $select = new Select($this->tableName);
        $select->where(['lesson_id'=>$row->lesson_id,'sort_order > '.$sortOrder])
            ->order('sort_order')
            ->limit(1);

        $rowset = $this->tableGateway->selectWith($select);
        if($rowset->count() ==0){
            return false;
        }
        else{
            return $rowset->current();
        }

    }


}