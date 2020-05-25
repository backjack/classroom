<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/24/2017
 * Time: 12:01 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class DiscussionAccountTable extends BaseTable {

    protected $tableName ='discussion_account';
    protected $primary = 'discussion_account_id';

    public function getDiscussionAccounts($id){
        $select = new Select($this->tableName);
        $select->join('accounts',$this->tableName.'.account_id=accounts.account_id',['first_name','last_name','email']);
        $select->where(['discussion_id'=>$id]);
        return $this->tableGateway->selectWith($select);
    }

    public function getTotalDiscussionAccounts($id){
        return $this->tableGateway->select(['discussion_id'=>$id])->count();
    }

    public function deleteAccountRecord($discussionId,$accountId){
        $total = $this->tableGateway->delete(['discussion_id'=>$discussionId,'account_id'=>$accountId]);
        return $total;
    }

    public function hasDiscussion($accountId,$discussionId){
        $total = $this->tableGateway->select(['account_id'=>$accountId,'discussion_id'=>$discussionId])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

}