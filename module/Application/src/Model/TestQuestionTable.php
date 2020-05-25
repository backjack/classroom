<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 1:35 PM
 */

namespace Application\Model;


use Application\Entity\TestQuestion;
use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TestQuestionTable extends BaseTable {
    protected $tableName ='test_question';
    protected $primary ='test_question_id';

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order('sort_order')
                ->where(['test_id'=>$id]);


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTotalQuestions($id){
        $total = $this->tableGateway->select(['test_id'=>$id])->count();
        return $total;
    }

    public function getLastSortOrder($testId){
        $row = TestQuestion::where('test_id',$testId)->orderBy('sort_order','desc')->first();
        if($row){
            return $row->sort_order;
        }
        else{
            return 0;
        }
    }

}