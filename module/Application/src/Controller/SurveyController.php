<?php
namespace Application\Controller;

use Dompdf\Dompdf;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Application\Entity\Session;
use Application\Entity\Student;
use Application\Entity\SurveyResponse;
use Application\Entity\Survey;
use Application\Model\AttendanceTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Application\Model\SurveyResponseOptionTable;
use Application\Model\SurveyResponseTable;
use Application\Model\SurveyOptionTable;
use Application\Model\SurveyQuestionTable;
use Application\Model\SurveyTable;

class SurveyController  extends AbstractController
{


    use HelperTrait;

     public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        if($this->studentIsLoggedIn()){
            $events->attach('dispatch', function ($e) use ($controller) {
                $controller->layout('layout/student');
            }, 100);
        }
        else{
            $events->attach('dispatch', function ($e) use ($controller) {
                $controller->layout('layout/survey');
            }, 100);
        }

    }


    public function surveyAction(){

        $output = [];
        $hash = trim($this->params('hash'));
        $survey = Survey::where('hash',$hash)->where('status',1)->first();
        $surveyTable = new SurveyTable();

        if(!$survey){
            return $this->showMessage(__('invalid-survey'));

        }

        //check if survey is private
        if($survey->private==1){
            if(!$this->studentIsLoggedIn()){
                $this->flashMessenger()->addMessage(__('private-survey-error'));
                return $this->redirect()->toRoute('application/signin');
            }

            //check if student is enrolled into any session of the survey
            $rowset = $surveyTable->getStudentSurveyRecords($this->getId(),$survey->survey_id);
            $total = $rowset->count();


            if(empty($total)){
                return $this->showMessage(__('no-survey-permission'));
            }

        }


        if($this->request->isPost()){

            $data = [];
            $data['created_on'] = time();

            if($this->studentIsLoggedIn()){
                $data['student_id'] = $this->getId();
            }

            $surveyResponse = $survey->surveyResponses()->create($data);

            $data = $this->request->getPost();

            foreach($data as $key=>$value){
                    if(preg_match('#question_#',$key)){
                  //      $questionId= intval(str_ireplace('question_','',$key));
                 //       echo "Qid {$questionId} | Val {$value} <br/>";

                        $surveyResponse->surveyResponseOptions()->create([
                           'survey_option_id'=>$value
                        ]);

                    }

            }

            return $this->redirect()->toRoute('survey-complete');

        }

        $output['pageTitle'] = __('survey').': '.$survey->name;
        $output['survey'] = $survey;
        $output['loggedIn'] = $this->studentIsLoggedIn();
        $output['totalQuestions'] = $survey->surveyQuestions()->count();
        return $output;            

    }

    public function completeAction(){
        return ['pageTitle'=>__('survey-submitted')];
    }


    public function showMessage($message){
        $viewModel = new ViewModel(['message'=>$message,'pageTitle'=>__('survey')]);
        $viewModel->setTemplate('application/survey/message');
        return $viewModel;
    }


}