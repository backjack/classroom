<?php


namespace Application\Model;

use Application\Entity\SurveyQuestion;
use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SurveyQuestionTable extends BaseTable
{
    protected $tableName = 'survey_question';
    protected $primary = 'survey_question_id';

    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order('sort_order')
            ->where(['survey_id'=>$id]);


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
        $total = $this->tableGateway->select(['survey_id'=>$id])->count();
        return $total;
    }

    public function getLastSortOrder($surveyId){
        $row = SurveyQuestion::where('survey_id',$surveyId)->orderBy('sort_order','desc')->first();
        if($row){
            return $row->sort_order;
        }
        else{
            return 0;
        }
    }

}