<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/8/2017
 * Time: 2:47 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class DiscussionReplyTable extends BaseTable {

    protected $tableName = 'discussion_reply';
    protected $primary = 'discussion_reply_id';


    public function getTotalReplies($id){
        return $this->tableGateway->select(['discussion_id'=>$id])->count();
    }

    public function getPaginatedRecordsForDiscussion($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(['discussion_id'=>$id]);

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getRepliedAdmins($id){
        return $this->tableGateway->select(['discussion_id'=>$id,'user_type'=>'a']);
    }

}