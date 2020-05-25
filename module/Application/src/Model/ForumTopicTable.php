<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/9/2018
 * Time: 4:04 PM
 */

namespace Application\Model;


use Application\Entity\Account;
use Application\Entity\ForumTopic;
use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ForumTopicTable extends BaseTable {
    protected $tableName = 'forum_topic';
    public $total;

    public function getTopicsForSession($sessionId){

        $select = new Select($this->tableName);
        $select->where(['session_id'=>$sessionId])
                ->order($this->primary.' desc');

        $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;

    }

    public function getTopicsForAdmin($adminId,$sessionId=null){

        if(GLOBAL_ACCESS){
            /*
            $select = new Select($this->tableName);
            $select->order($this->primary.' desc');
            $topics = ForumTopic::orderBy($this->primary,'desc')->paginate(20);
            return $topics;
            */
            $select = new Select($this->tableName);
            $select->join('session',$this->tableName.'.session_id=session.session_id',['session_name']);
            if(!empty($sessionId)){
                $select->where([$this->tableName.'.session_id'=>$sessionId]);
            }
            $select->order($this->primary.' desc');
        }
        else{
            //get topics for session created by admin
            $select = new Select($this->tableName);
            $select->order($this->primary.' desc')
                ->join('session',$this->tableName.'.session_id=session.session_id',['session_name'])
                ->where(['session.account_id'=>$adminId])
                ->columns(['topic_title','forum_topic_id','topic_owner','created_on','topic_owner_type','session_id']);

            if(!empty($sessionId)){
                $select->where([$this->tableName.'.session_id'=>$sessionId]);
            }
            //get topics for sessions where admin is instructor
            $select2 = new Select($this->tableName);
            $select2->order($this->primary.' desc')
                ->join('session',$this->tableName.'.session_id=session.session_id',['session_name'])
                ->join('session_instructor',$this->tableName.'.session_id=session_instructor.session_id',[])
                ->where(['session_instructor.account_id'=>$adminId])
                ->columns(['topic_title','forum_topic_id','topic_owner','created_on','topic_owner_type','session_id']);
            if(!empty($sessionId)){
                $select2->where([$this->tableName.'.session_id'=>$sessionId]);
            }
            $select->combine($select2);

        }

        $newSelect = $select;
        $this->total= $this->tableGateway->selectWith($newSelect)->count();
        $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

}