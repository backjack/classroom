<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/5/2017
 * Time: 11:33 AM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LecturePageTable extends BaseTable {

    protected $tableName = 'lecture_page';
    protected $primary = 'lecture_page_id';

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order('sort_order');
        $select->where(['lecture_id'=>$id]);

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getTotalLecturePages($id){
        $total = $this->tableGateway->select(['lecture_id'=>$id])->count();
        return $total;
    }

    public function getRecordsOrdered($id){
        $select = new Select($this->tableName);
        $select->order('sort_order')
                ->where(['lecture_id'=>$id]);
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getNextSortOrder($id){
        $select = new Select($this->tableName);
        $select->order('sort_order desc');
        $select->limit(1);
        $select->where(['lecture_id'=>$id]);
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
            $this->update(['sort_order'=>$count],$row->lecture_page_id);
            $count++;
        }

    }
    public function getPreviousPage($id){
        $row = $this->getRecord($id);
        $sortOrder = $row->sort_order;
        $select = new Select($this->tableName);
        $select->where(['lecture_id'=>$row->lecture_id,'sort_order < '.$sortOrder])
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

    public function getNextPage($id){
        $row = $this->getRecord($id);
        $sortOrder = $row->sort_order;
        $select = new Select($this->tableName);
        $select->where(['lecture_id'=>$row->lecture_id,'sort_order > '.$sortOrder])
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