<?php
namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SurveyResponseOptionTable extends BaseTable
{
    protected $tableName = 'survey_response_option';
    protected $primary = 'survey_response_option_id';


    public function getSurveyRecords($id)
    {
        $select = new Select($this->tableName);
        $select->where(['survey_response_id'=>$id])
            ->join('survey_option',$this->tableName.'.survey_option_id=survey_option.survey_option_id')
            ->join('survey_question','survey_option.survey_question_id=survey_question.survey_question_id');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    /**
     * Get the total number of responses for an option
     */
    public function getOptionCount($id){
        $select = new Select($this->tableName);
        $select->where(['survey_option_id'=>$id]);

        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->count();
    }

    /**
     * Get the total number of responses for a question
     */
    public function getQuestionCount($id){
        $select = new Select($this->tableName);
        $select->where(['survey_question_id'=>$id])
            ->join('survey_option',$this->tableName.'.survey_option_id=survey_option.survey_option_id');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->count();
    }
}