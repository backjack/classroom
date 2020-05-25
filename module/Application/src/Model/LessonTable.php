<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/7/2016
 * Time: 3:23 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LessonTable extends BaseTable {

    protected $tableName = 'lesson';
    protected $primary = 'lesson_id';
    protected $accountId = true;

    public function getRecords(){
        $select = new Select($this->tableName);
        $select->order('lesson_name asc');

        if(!GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }

        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getLimitedLessonRecords($type=null,$limit=null){
        $select = new Select($this->tableName);
        $select->order('lesson_name asc');
        if($type){
            $select->where(['lesson_type'=>$type]);
        }
        if($limit){
            $select->limit($limit);
        }

        if(!GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }

        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getRecordsOrdered(){
        $select = new Select($this->tableName);
        $select->order('sort_order');
        if(!GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getLessons($paginated=false,$filter=null,$group=null,$order=null)
    {
        if(empty($group)){
            $select = new Select($this->tableName);
        }
        else{
            $select = new Select('lesson_to_lesson_group');
            $select->join('lesson','lesson_to_lesson_group.lesson_id=lesson.lesson_id',['lesson_name','sort_order','lesson_type']);
            $select->join('lesson_group','lesson_to_lesson_group.lesson_group_id=lesson_group.lesson_group_id',['group_name']);
            $select->where(['lesson_group.lesson_group_id'=>$group]);
        }

        if(!GLOBAL_ACCESS){
            $select->where(['lesson.account_id'=>ADMIN_ID]);
        }

        switch($order){
            case 'asc':
                $select->order('lesson_name asc');
                break;
            case 'desc':
                $select->order('lesson_name desc');
                break;
            case 'recent':
                $select->order('lesson_id desc');
                break;
            case 'sortOrder':
                $select->order('sort_order asc');
                break;
            case 'session':
                $select->where(['lesson_type'=>'s']);
                break;
            case 'online':
                $select->where(['lesson_type'=>'c']);
                break;
            default:
                $select->order('lesson_id desc');
                break;

        }


        if(isset($filter))
        {
            $filter = $this->db->escape($filter);

            $select->where("MATCH (lesson.lesson_name,lesson.content) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)");

        }



        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }



}