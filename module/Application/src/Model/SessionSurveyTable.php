<?php
namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class SessionSurveyTable  extends BaseTable
{

    protected $tableName = 'session_survey';
    protected $primary = 'session_survey_id';


    public function getSessionRecords($id){

        $select = new Select($this->tableName);
        $select->where([$this->tableName.'.session_id'=>$id])
            ->join('survey','session_survey.survey_id=survey.survey_id',['name','survey_id','account_id','survey_status'=>'status','private'])
            ->join('session','session_survey.session_id=session.session_id',['session_name'])
            ->order($this->tableName.'.opening_date');

        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }

    public function getSurveyRecords($id){
        $select = new Select($this->tableName);
        $select->where([$this->tableName.'.survey_id'=>$id])
            ->join('survey','session_survey.survey_id=survey.survey_id',['name'])
            ->join('session','session_survey.session_id=session.session_id',['session_name'])
            ->order($this->tableName.'.session_survey_id desc');

        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;
    }


    public function getUpcomingSurveys($days){
        $upperLimit = strtotime('tomorrow midnight') - 1;

        $timestamp = strtotime("+$days day");

        $timeLimit = mktime(23,59,0,date('n',$timestamp),date('j',$timestamp),date('Y',$timestamp));


        $select = new Select($this->tableName);
        $select->where(['opening_date < '.$timeLimit,'opening_date > '.$upperLimit])
            ->where(['opening_date > 0'])
            ->join('survey','session_survey.survey_id=survey.survey_id',['name'])
            ->join('session',$this->tableName.'.session_id=session.session_id',['session_name'])
            ->order('opening_date');
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }


}