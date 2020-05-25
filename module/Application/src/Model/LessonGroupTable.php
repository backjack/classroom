<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/7/2017
 * Time: 1:47 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LessonGroupTable extends BaseTable{

    protected $tableName='lesson_group';
    protected $primary = 'lesson_group_id';

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order('sort_order asc');

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getLimitedRecords($limit)
    {
        $select = new Select($this->tableName);
        $select->order('sort_order asc');
        $select->limit($limit);


        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }



}