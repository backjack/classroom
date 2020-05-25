<?php

namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SurveyTable extends BaseTable{

    protected $tableName = 'survey';
    protected $primary = 'survey_id';
    protected $accountId = true;


    public function getPaginatedRecords($paginated=false,$id=null,$filter=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');

        if($this->accountId && !GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }

        if(isset($filter))
        {
            $filter = $this->db->escape($filter);
            $select->where('(survey.name LIKE \'%'.$filter.'%\')');
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

    public function getStudentSurveyRecords($studentId,$surveyId){
        $today = time();
        $select1 = new Select('student_session');
        $select1->join('session_survey','student_session.session_id=session_survey.session_id',array())
            ->join('survey','session_survey.survey_id=survey.survey_id',['survey_id','name','status','minutes','private'])
            ->where(['student_session.student_id'=>$studentId])
            ->where(['survey.status'=>'1'])
            ->where(['survey.survey_id'=>$surveyId])
            ->columns(['session_id'])
            ->order('survey.created_on desc');

        $rowset = $this->tableGateway->selectWith($select1);
        $rowset->buffer();
        return $rowset;
    }

    public function getStudentRecords($studentId){

        $today = time();
        $select1 = new Select('student_session');
        $select1->join('session_survey','student_session.session_id=session_survey.session_id',array())
            ->join('survey','session_survey.survey_id=survey.survey_id',['survey_id','name','status','private','hash'])
            ->where(['student_session.student_id'=>$studentId])
            ->where(['survey.status'=>'1'])
            ->columns([])
            ->group('session_survey.survey_id')
            ->order('survey.created_on desc');

        $select2 = new Select($this->tableName);
        $select2->where(['private'=>0])
            ->where(['survey.status'=>'1'])
            ->columns(['survey_id','name','status','private','hash']);

        $select1->combine($select2);

        //  $sql = $select1->getSqlString();
        //   exit($sql);
        $paginatorAdapter = new DbSelect($select1,$this->tableGateway->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;



    }



}

?>