<?php

namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SurveyOptionTable extends BaseTable{

    protected $tableName = 'survey_option';
    protected $primary = 'survey_option_id';

    public function getTotalOptions($id){
        $total = $this->tableGateway->select(['survey_question_id'=>$id])->count();
        return $total;
    }

    public function getOptionRecords($id){
        $rowset = $this->tableGateway->select(['survey_question_id'=>$id]);
        return $rowset;
    }


    public function getOptionRecordsPaginated($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(['survey_question_id'=>$id]);

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

?>