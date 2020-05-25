<?php
namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Entity\SessionSurvey;
use Application\Entity\Survey;
use Application\Entity\SurveyOption;
use Application\Entity\SurveyQuestion;
use Application\Entity\SurveyResponseOption;
use Application\Form\SurveyFilter;
use Application\Form\SurveyForm;
use Application\Form\TestQuestionFilter;
use Application\Form\TestQuestionForm;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionSurveyTable;
use Application\Model\SessionTable;
use Application\Model\SurveyOptionTable;
use Application\Model\SurveyQuestionTable;
use Application\Model\SurveyResponseOptionTable;
use Application\Model\SurveyResponseTable;
use Application\Model\SurveyTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputFilter;
use Zend\View\Model\ViewModel;

class SurveyController extends AbstractController
{

    use HelperTrait;

    public function indexAction(){

        $table = new SurveyTable($this->getServiceLocator());
        $questionTable = new SurveyQuestionTable($this->getServiceLocator());
        $studentSurveyTable = new SurveyResponseTable($this->getServiceLocator());
        $filter = $this->params()->fromQuery('filter', null);



        if (empty($filter)) {
            $filter=null;
        }
        $paginator = $table->getPaginatedRecords(true,null,$filter);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'pageTitle'=>__('surveys'),
            'questionTable'=>$questionTable,
            'studentSurveyTable'=>$studentSurveyTable
        ));
    }


    public function addAction()
    {
        $output = array();
        $table = new SurveyTable($this->getServiceLocator());
        $form = new SurveyForm(null,$this->getServiceLocator());
        $filter = new SurveyFilter();

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                $array['created_on'] = time();
                $array['hash'] = str_random(40);
                $array[$table->getPrimary()]=0;
                $id= $table->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                $output['message'] = __('Record Added!');
                $this->flashMessenger()->addMessage(__('survey-add-msg'));
                $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'questions','id'=>$id]);
            }
            else{
                $output['message'] = __('save-failed-msg');

            }

        }

        $output['form'] = $form;
        $output['pageTitle']= __('Add Survey');
        $output['action']='add';
        $output['id']=null;
        return new ViewModel($output);
    }

    public function editAction(){
        $output = array();
        $table = new SurveyTable($this->getServiceLocator());
        $form = new SurveyForm(null,$this->getServiceLocator());
        $filter = new SurveyFilter();
        $id = $this->params('id');

        $row = $table->getRecord($id);
        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {



                $array = $form->getData();
                $array[$table->getPrimary()]=$id;
                $table->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                $output['message'] = __('Changes Saved!');
                $row = $table->getRecord($id);
            }
            else{
                $output['message'] = __('save-failed-msg');
            }

        }
        else {

            $data = UtilityFunctions::getObjectProperties($row);

            $form->setData($data);

        }


        $output['form'] = $form;
        $output['id'] = $id;
        $output['pageTitle']= __('Edit Survey');
        $output['row']= $row;
        $output['action']='edit';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/survey/add');
        return $viewModel ;

    }

    public function deleteAction()
    {
        $table = new SurveyTable($this->getServiceLocator());
        $id = $this->params('id');
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'survey','action'=>'index'));
    }


    public function questionsAction(){
        $testTable = new SurveyTable($this->getServiceLocator());

        $id = $this->params('id');
        $table = new SurveyQuestionTable($this->getServiceLocator());
        $optionTable = new SurveyOptionTable($this->getServiceLocator());
        $row = $testTable->getRecord($id);
        $paginator = $table->getPaginatedRecords(true,$id);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Survey Questions').': '.$row->name,
            'id'=>$id,
            'optionTable'=>$optionTable,
            'page'=>(int)$this->params()->fromQuery('page', 1)
        ));
    }


    public function addquestionAction()
    {
        $id= $this->params('id');
        $testQuestionTable = new SurveyQuestionTable($this->getServiceLocator());
        $testOptionTable = new SurveyOptionTable($this->getServiceLocator());
        if($this->request->isPost())
        {
            $data = $this->request->getPost();
            if(!empty($data['question'])){

                $dbData= [
                    'survey_id'=>$id,
                    'question'=>$data['question'],
                    'sort_order'=>$data['sort_order']
                ];

                $questionId = $testQuestionTable->addRecord($dbData);
                $this->flashMessenger()->addMessage(__('Question added'));
                //correct answer
                 
                for($i=1;$i<=5;$i++){

                    if(!empty($data['option_'.$i])){

                        $optionData = [
                            'survey_question_id'=>$questionId,
                            'option'=> trim($data['option_'.$i])
                        ];

                     

                        $testOptionTable->addRecord($optionData);
                    }


                }


            }
        }

        return $this->goBack();
        // return $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'questions','id'=>$id]);
    }

    public function editquestionAction(){

        $output = [];
        $id = $this->params('id');
        $questionTable = new SurveyQuestionTable($this->getServiceLocator());
        $optionTable = new SurveyOptionTable($this->getServiceLocator());
        $form = new TestQuestionForm();
        $filter = new TestQuestionFilter();
        $form->setInputFilter($filter);
        $row = $questionTable->getRecord($id);
        $rowset = $optionTable->getOptionRecords($id);



        if($this->request->isPost())
        {
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();
                $questionTable->update($data,$id);
                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'questions','id'=>$row->survey_id]);
            }
            else{
                $output['message'] = __('save-failed-msg');
            }
        }
        else{
            $form->setData(UtilityFunctions::getObjectProperties($row));
        }


        $output['row'] = $row;
        $output['rowset']= $rowset;
        $output['form'] = $form;
        $output['id'] = $id;
        $output['pageTitle'] = __('Edit Question/Options');
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'survey','action'=>'index'])=>__('Surveys'),
            $this->url()->fromRoute('admin/default',['controller'=>'survey','action'=>'questions','id'=>$row->survey_id])=>__('Survey Questions'),
            '#'=>__('Edit Question')
        ];
        return $output;
    }

    public function addoptionsAction()
    {
        $id= $this->params('id');
        $testOptionTable = new SurveyOptionTable($this->getServiceLocator());
        if($this->request->isPost())
        {
            $data = $this->request->getPost();


            //correct answer

            $count = 0;
            for($i=1;$i<=5;$i++){

                if(!empty($data['option_'.$i])){

                    $optionData = [
                        'survey_question_id'=>$id,
                        'option'=> trim($data['option_'.$i])
                    ];


                    $testOptionTable->addRecord($optionData);
                    $count++;

                }


            }
            $this->flashMessenger()->addMessage($count.' '.__('options added'));


        }

        return $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'editquestion','id'=>$id]);

    }

    public function editoptionAction(){

        $testOptionTable = new SurveyOptionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $testOptionTable->getRecord($id);
        $questionId = $row->survey_question_id;

        if($this->request->isPost())
        {
            $data = $this->request->getPost();
            if(!empty($data['option'])) {

                $dbData = [];

                $dbData['option']=$data['option'];
                $testOptionTable->update($dbData,$id);
                $this->flashMessenger()->addMessage(__('Option saved'));
            }
            else{
                $this->flashMessenger()->addMessage(__('survey-save-failed'));
            }
            $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'editquestion','id'=>$questionId]);
        }

        $option = new Text('option');
        $option->setAttributes(['class'=>'form-control']);
        $option->setValue($row->option);



        $viewModel = new ViewModel(['row'=>$row,'option'=>$option,'id'=>$id]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function deletequestionAction()
    {
        $table = new SurveyQuestionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $table->getRecord($id);
        $testId = $row->survey_id;
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'survey','action'=>'questions','id'=>$testId));
    }

    public function deleteoptionAction()
    {
        $table = new SurveyOptionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $table->getRecord($id);
        $questionId = $row->survey_question_id;
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'survey','action'=>'editquestion','id'=>$questionId));
    }

    public function duplicateAction()
    {
        $testTable = new SurveyTable($this->getServiceLocator());
        $testQuestionTable = new SurveyQuestionTable($this->getServiceLocator());
        $testOptionTable = new SurveyOptionTable($this->getServiceLocator());
        $id = $this->params('id');

        //get all questions
        $test = $testTable->getRecord($id);
        $questions = $testQuestionTable->getPaginatedRecords(false,$test->survey_id)->toArray();
        $options = [];
        foreach($questions as $question){
            $options[$question['survey_question_id']] = $testOptionTable->getOptionRecords($question['survey_question_id'])->toArray();
        }


        $testData = UtilityFunctions::getObjectProperties($test);
        $testData['created_on'] = time();
        unset($testData['survey_id']);

        $newId = $testTable->addRecord($testData);

        foreach($questions as $question)
        {
            $oldQuestionId=$question['survey_question_id'];
            $question['survey_id']= $newId;
            unset($question['survey_question_id']);
            $questionId=  $testQuestionTable->addRecord($question);
            foreach($options[$oldQuestionId] as $option){
                $option['survey_question_id'] = $questionId;
                unset($option['survey_option_id']);
                $testOptionTable->addRecord($option);
            }

        }

        $this->flashMessenger()->addMessage(__('Survey duplicated'));
        $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'index']);



    }


    public function resultsAction()
    {
        $testTable = new SurveyTable($this->getServiceLocator());
        $table = new SurveyResponseTable($this->getServiceLocator());
        $id = $this->params('id');

        $filter = $this->params()->fromQuery('filter', null);
        $startDate = $this->params()->fromQuery('start', null) ? strtotime($this->params()->fromQuery('start', null)):null;
        $endDate = $this->params()->fromQuery('end', null) ? strtotime($this->params()->fromQuery('end', null)):null ;

        if (empty($filter)) {
            $filter=null;
        }


        $row= $testTable->getRecord($id);
        $paginator = $table->getPaginatedRecords(true,$id);

        $testTotal = $table->getTotalForTest($id,$startDate,$endDate);


        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Survey results').':('.$testTotal.') '.$row->name,
            'row'=>$row
        ));
    }

    public function deleteresultAction()
    {
        $studentSurveyTable = new SurveyResponseTable($this->getServiceLocator());

        $id = $this->params('id');
        $row = $studentSurveyTable->getRecord($id);
        $testId = $row->survey_id;
        try{
            $studentSurveyTable->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'survey','action'=>'results','id'=>$testId));
    }

    public function resultAction()
    {
        $studentSurveyTable = new SurveyResponseTable($this->getServiceLocator());
        $studentOptionTable = new SurveyResponseOptionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $studentSurveyTable->getRecord($id);
        $rowset = $studentOptionTable->getSurveyRecords($id);
        $data = ['row'=>$row,'rowset'=>$rowset];
        $viewModel = new ViewModel($data);
        $viewModel->setTerminal(true);
        return $viewModel;

    }

    public function exportresultAction(){

        $type = $_GET['type'];
        $studentSurveyTable = new SurveyResponseTable($this->getServiceLocator());
        $testTable = new SurveyTable($this->getServiceLocator());
        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $startDate = $this->params()->fromQuery('start', null) ? strtotime($this->params()->fromQuery('start', null)):null;
        $endDate = $this->params()->fromQuery('end', null) ? strtotime($this->params()->fromQuery('end', null)):null ;


        $myfile = fopen($file, "w") or die("Unable to open file!");
        $id = $this->params('id');
        $testRow = $testTable->getRecord($id);
        if($type=='pass')
        {
            $totalRecords = $studentSurveyTable->getTotalPassedForTest($id,$testRow->passmark,$startDate,$endDate);
        }
        else{
            $totalRecords = $studentSurveyTable->getTotalFailedForTest($id,$testRow->passmark,$startDate,$endDate);
        }

        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);
        fputcsv($myfile, array('First Name','Last Name','Score%'));
        for($i=1;$i<=$totalPages;$i++){
            if($type=='pass') {
                $paginator = $studentSurveyTable->getPassedPaginatedRecords(true, $id,$testRow->passmark,$startDate,$endDate);
            }
            else{
                $paginator = $studentSurveyTable->getFailPaginatedRecords(true, $id,$testRow->passmark,$startDate,$endDate);
            }

            $paginator->setCurrentPageNumber($i);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row){

                fputcsv($myfile, array($row->first_name,$row->last_name,$row->score));

            }



        }
        $paginator = array();
        fclose($myfile);
        header('Content-type: text/csv');
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.$type.'_student_test_export_'.date('d/M/Y').'.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();
    }


    public function sessionsAction(){

        $id = $this->params('id');
        $sessionSurveyTable = new SessionSurveyTable($this->getServiceLocator());
        $surveyTable = new  SurveyTable($this->getServiceLocator());
        $surveyRow = $surveyTable->getRecord($id);

        $rowset = $sessionSurveyTable->getSurveyRecords($id);
        return [
            'pageTitle'=>__('survey-sessions-courses').': '.$surveyRow->name,
            'rowset'=>$rowset,
            'id'=>$id
        ];

    }

    public function addsessionAction(){
        $id = $this->params('id');
        $sessionSurveyTable = new SessionSurveyTable($this->getServiceLocator());
        $testTable = new SurveyTable($this->getServiceLocator());
        $testRow = $testTable->getRecord($id);
        $form = $this->getSessionTestForm();
        $output = [];
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){

                $data = $form->getData();
                $data['survey_id'] = $id;
                $sessionSurveyTable->addRecord($data);
                $this->flashMessenger()->addMessage(__('course-added-succ'));
                $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'sessions','id'=>$id]);


            }
            else{
                $output['message']= $this->getFormErrors($form);
            }
        }

        $output['form'] = $form;
        $output['pageTitle'] = __('add-course-to').' '.$testRow->name;
        $output['id']=$id;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'survey','action'=>'index'])=>__('surveys'),
            $this->url()->fromRoute('admin/default',['controller'=>'survey','action'=>'sessions','id'=>$id])=>__('Sessions/Courses'),
            '#'=>__('add').' '.__('sessions-courses')
        ];
        return $output;
    }

    public function editsessionAction(){
        $id = $this->params('id');
        $sessionSurveyTable = new SessionSurveyTable($this->getServiceLocator());
        $row = $sessionSurveyTable->getRecord($id);
        $testTable = new SurveyTable($this->getServiceLocator());
        $testRow = $testTable->getRecord($row->survey_id);
        $form = $this->getSessionTestForm();
        $output = [];
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){

                $data = $form->getData();

                $sessionSurveyTable->update($data,$id);
                $this->flashMessenger()->addMessage(__('course-saved'));
                $this->redirect()->toRoute('admin/default',['controller'=>'survey','action'=>'sessions','id'=>$testRow->survey_id]);


            }
            else{
                $output['message']= $this->getFormErrors($form);
            }
        }
        else{
            $data = UtilityFunctions::getObjectProperties($row);

            $form->setData($data);

        }

        $output['form'] = $form;
        $output['pageTitle'] = __('edit-course-for').' '.$testRow->name;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'survey','action'=>'index'])=>__('surveys'),
            $this->url()->fromRoute('admin/default',['controller'=>'survey','action'=>'sessions','id'=>$id])=>__('Sessions/Courses'),
            '#'=>__('edit').' '.__('sessions-courses')
        ];
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/survey/addsession');
        return $viewModel;
    }

    public function deletesessionAction(){
        $id = $this->params('id');
        $testTable = new SurveyTable($this->getServiceLocator());
        $sessionSurveyTable= new SessionSurveyTable($this->getServiceLocator());
        $row = $sessionSurveyTable->getRecord($id);
        $testRow = $testTable->getRecord($row->survey_id);
        if($testRow->account_id==$this->getAdminId() || GLOBAL_ACCESS){
            $sessionSurveyTable->deleteRecord($id);
            $this->flashMessenger()->addMessage(__('Record deleted'));
        }
        else{
            $this->flashMessenger()->addMessage('You do not have permission to do this');
        }

        $this->goBack();

    }

    private function getSessionTestForm(){
        $form = new BaseForm();

        //get all sessions for user
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessions = $sessionTable->getPaginatedRecords(true);
        $sessions->setCurrentPageNumber(1);
        $sessions->setItemCountPerPage(500);
        $options=array();
        foreach ($sessions as $row)
        {
            $options[$row->session_id]=$row->session_name;
        }

        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $rowset = $sessionInstructorTable->getAccountRecords(ADMIN_ID);
        foreach($rowset as $row){
            $options[$row->session_id] = $row->session_name;
        }

        $form->createSelect('session_id', __('Session/Course'), $options);
        $form->get('session_id')->setAttribute('class','form-control select2');



        $form->setInputFilter($this->getSessionTestFilter());
        return $form;


    }

    private function getSessionTestFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'session_id',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        return $filter;

    }


    public function importquestionsAction(){

        $id = $this->params('id');

        $testQuestionTable = new SurveyQuestionTable();

        $lastSortOrder = $testQuestionTable->getLastSortOrder($id);

        if($this->request->isPost()){
            $post = $this->request->getPost();
            $data = $_FILES['file'];
            $sessionId = $post['session_id'];
            $file = $data['tmp_name'];
            $file = fopen($file,"r");

            $all_rows = array();
            $header = null;
            while ($row = fgetcsv($file)) {
                if ($header === null) {
                    $header = $row;
                    continue;
                }
                $all_rows[] = array_combine($header, $row);
            }
            $imported=0;
            foreach($all_rows as $value){


                $question = $value['Question'];

                if(empty($question)){
                    continue;
                }
                $options = $value['Options'];


                //create new question
                $testQuestion = new SurveyQuestion();
                $testQuestion->question = trim($question);
                if(!empty($lastSortOrder)){
                    $lastSortOrder++;
                    $testQuestion->sort_order = $lastSortOrder;
                }
                else{
                    $testQuestion->sort_order = 0 ;
                }

                $testQuestion->survey_id = $id;
                $testQuestion->save();
                $imported++;
                //get options
                $optionEntries= explode(',',$options);
                $count =0;
                foreach ($optionEntries as $optionValue){

                    if(!empty($optionValue)){
                        $count++;
                        $testOption=new SurveyOption();
                        $testOption->survey_question_id= $testQuestion->survey_question_id;
                        $testOption->option = trim($optionValue);

                        $testOption->save();
                    }

                }


            }

            $this->flashMessenger()->addMessage(__('questions-imported',['count'=>$imported]));
            return $this->goBack();

        }



    }

    public function exportquestionsAction(){

        $id = $this->params('id');
        $survey =  Survey::find($id);

        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die(__('unable-to-open'));

        $columns = array(__('Question'),__('Options'));
        fputcsv($myfile,$columns);

        foreach ($survey->surveyQuestions()->orderBy('sort_order')->get() as $surveyQuestion){

            $data = [];
            $data[0] = strip_tags($surveyQuestion->question);

            $optionCount = 0;
            $correct = 0;
            $optionArray = [];
            foreach ($surveyQuestion->surveyOptions as $surveyOption){
                $optionCount++;
                $optionArray[] = $surveyOption->option;

            }
            $data[1] = implode(',',$optionArray);
            fputcsv($myfile,$data);
        }

        fclose($myfile);
        header('Content-type: text/csv');
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.safeUrl($survey->name).'_questions_'.date('d/M/Y').'.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();

    }


    public function sendAction(){
        $id = $this->params('id');
        $sessionSurvey = SessionSurvey::find($id);

        $count = 0;


        foreach($sessionSurvey->session->studentSessions as $student){
            try{
                $this->mailSurvey($sessionSurvey->survey_id,$student->student_id);
                $count++;
            }
            catch(\Exception $ex){

            }


        }
        $this->flashMessenger()->addMessage(__('survey-sent-msg',['count'=>$count]));
        return $this->goBack();
    }


    public function reportAction(){
        $id = $this->params('id');
        $survey = Survey::find($id);

        return [
          'pageTitle'=>__('survey-report').': '.$survey->name,
            'survey'=>$survey,
            'controller'=>$this
        ];
    }


    public function getOptionPercent($id){
        $surveyResponseOption = SurveyResponseOption::find($id);
        $surveyOption = SurveyOption::find($id);
        $questionID = $surveyOption->survey_question_id;

        $table = new SurveyResponseOptionTable();
        $totalForOption = $table->getOptionCount($id);
        $totalForQuestion = $table->getQuestionCount($questionID);

        if($totalForQuestion < 1){
            $totalForQuestion = 1;
        }

        $percentage = ($totalForOption/$totalForQuestion) * 100;

        $percentage = round($percentage);
        return $percentage;
    }
}