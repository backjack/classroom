<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 1:39 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Entity\Test;
use Application\Entity\TestOption;
use Application\Entity\TestQuestion;
use Application\Form\TestFilter;
use Application\Form\TestForm;
use Application\Form\TestQuestionFilter;
use Application\Form\TestQuestionForm;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionTable;
use Application\Model\SessionTestTable;
use Application\Model\StudentTestOptionTable;
use Application\Model\StudentTestTable;
use Application\Model\TestOptionTable;
use Application\Model\TestQuestionTable;
use Application\Model\TestTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Intermatics\UtilityFunctions;

class TestController extends AbstractController {

    use HelperTrait;

    public function indexAction(){

        $table = new TestTable($this->getServiceLocator());
        $questionTable = new TestQuestionTable($this->getServiceLocator());
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $filter = $this->params()->fromQuery('filter', null);



        if (empty($filter)) {
            $filter=null;
        }
        $paginator = $table->getPaginatedRecords(true,null,$filter);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Tests'),
            'questionTable'=>$questionTable,
            'studentTestTable'=>$studentTestTable
        ));
    }


    public function addAction()
    {
        $output = array();
        $table = new TestTable($this->getServiceLocator());
        $form = new TestForm(null,$this->getServiceLocator());
        $filter = new TestFilter();

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                $array['created_on'] = time();
                $array[$table->getPrimary()]=0;
               $id= $table->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                $output['message'] = __('Record Added!');
                $this->flashMessenger()->addMessage(__('test-added'));
                $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'questions','id'=>$id]);
            }
            else{
                $output['message'] = __('save-failed-msg');

            }

        }

        $output['form'] = $form;
        $output['pageTitle']= __('Add Test');
        $output['action']='add';
        $output['id']=null;
        return new ViewModel($output);
    }

    public function editAction(){
        $output = array();
        $table = new TestTable($this->getServiceLocator());
        $form = new TestForm(null,$this->getServiceLocator());
        $filter = new TestFilter();
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
        $output['pageTitle']= __('Edit Test');
        $output['row']= $row;
        $output['action']='edit';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/test/add');
        return $viewModel ;

    }

    public function deleteAction()
    {
        $table = new TestTable($this->getServiceLocator());
        $id = $this->params('id');
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'test','action'=>'index'));
    }


    public function questionsAction(){
        $testTable = new TestTable($this->getServiceLocator());

        $id = $this->params('id');
        $table = new TestQuestionTable($this->getServiceLocator());
        $optionTable = new TestOptionTable($this->getServiceLocator());
        $row = $testTable->getRecord($id);
        $paginator = $table->getPaginatedRecords(true,$id);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Test Questions').': '.$row->name,
            'id'=>$id,
            'optionTable'=>$optionTable,
            'page'=>(int)$this->params()->fromQuery('page', 1)
        ));
    }


    public function addquestionAction()
    {
        $id= $this->params('id');
        $testQuestionTable = new TestQuestionTable($this->getServiceLocator());
        $testOptionTable = new TestOptionTable($this->getServiceLocator());
        if($this->request->isPost())
        {
            $data = $this->request->getPost();
            if(!empty($data['question'])){

                $dbData= [
                  'test_id'=>$id,
                    'question'=>$data['question'],
                    'sort_order'=>$data['sort_order']
                ];

                $questionId = $testQuestionTable->addRecord($dbData);
                $this->flashMessenger()->addMessage(__('Question added'));
                 //correct answer
                $correct = $data['correct_option'];
                for($i=1;$i<=5;$i++){

                    if(!empty($data['option_'.$i])){

                        $optionData = [
                            'test_question_id'=>$questionId,
                          'option'=> trim($data['option_'.$i])
                        ];

                        if($i==$correct){
                            $optionData['is_correct'] = 1;
                        }
                        else{
                            $optionData['is_correct'] = 0;
                        }

                        $testOptionTable->addRecord($optionData);
                    }


                }


            }
        }

        return $this->goBack();
       // return $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'questions','id'=>$id]);
    }

    public function editquestionAction(){

        $output = [];
        $id = $this->params('id');
        $questionTable = new TestQuestionTable($this->getServiceLocator());
        $optionTable = new TestOptionTable($this->getServiceLocator());
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
                $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'questions','id'=>$row->test_id]);
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
            $this->url()->fromRoute('admin/default',['controller'=>'test','action'=>'index'])=>__('Tests'),
             $this->url()->fromRoute('admin/default',['controller'=>'test','action'=>'questions','id'=>$row->test_id])=>__('Test Questions'),
            '#'=>__('Edit Question')
        ];
        return $output;
    }

    public function addoptionsAction()
    {
        $id= $this->params('id');
        $testOptionTable = new TestOptionTable($this->getServiceLocator());
        if($this->request->isPost())
        {
            $data = $this->request->getPost();


                //correct answer
                $correct = $data['correct_option'];
                if(!empty($correct)){
                    $testOptionTable->clearIsCorrect($id);
                }
                $count = 0;
                for($i=1;$i<=5;$i++){

                    if(!empty($data['option_'.$i])){

                        $optionData = [
                            'test_question_id'=>$id,
                            'option'=> trim($data['option_'.$i])
                        ];

                        if($i==$correct){
                            $optionData['is_correct'] = 1;
                        }
                        else{
                            $optionData['is_correct'] = 0;
                        }

                        $testOptionTable->addRecord($optionData);
                        $count++;

                    }


                }
            $this->flashMessenger()->addMessage($count.' '.__('options added'));


        }

        return $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'editquestion','id'=>$id]);

    }

    public function editoptionAction(){

        $testOptionTable = new TestOptionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $testOptionTable->getRecord($id);
        $questionId = $row->test_question_id;

        if($this->request->isPost())
        {
            $data = $this->request->getPost();
            if(!empty($data['option'])) {

                $dbData = [];
                if (!empty($data['is_correct']))
                {
                    $testOptionTable->clearIsCorrect($questionId);
                    $dbData['is_correct']=$data['is_correct'];
                }
                $dbData['option']=$data['option'];
                $testOptionTable->update($dbData,$id);
                $this->flashMessenger()->addMessage(__('Option saved'));
            }
            else{
                $this->flashMessenger()->addMessage(__('survey-save-failed'));
            }
            $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'editquestion','id'=>$questionId]);
        }

        $option = new Text('option');
        $option->setAttributes(['class'=>'form-control']);
        $option->setValue($row->option);

        $select = new Select('is_correct');
        $select->setAttribute('class','form-control');
        $select->setValueOptions([1=>'Yes',0=>'No']);
        $select->setValue($row->is_correct);

        $viewModel = new ViewModel(['row'=>$row,'option'=>$option,'select'=>$select,'id'=>$id]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function deletequestionAction()
    {
        $table = new TestQuestionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $table->getRecord($id);
        $testId = $row->test_id;
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'test','action'=>'questions','id'=>$testId));
    }

    public function deleteoptionAction()
    {
        $table = new TestOptionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $table->getRecord($id);
        $questionId = $row->test_question_id;
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'test','action'=>'editquestion','id'=>$questionId));
    }

    public function duplicateAction()
    {
        $testTable = new TestTable($this->getServiceLocator());
        $testQuestionTable = new TestQuestionTable($this->getServiceLocator());
        $testOptionTable = new TestOptionTable($this->getServiceLocator());
        $id = $this->params('id');

        //get all questions
        $test = $testTable->getRecord($id);
        $questions = $testQuestionTable->getPaginatedRecords(false,$test->test_id)->toArray();
        $options = [];
        foreach($questions as $question){
            $options[$question['test_question_id']] = $testOptionTable->getOptionRecords($question['test_question_id'])->toArray();
        }


        $testData = UtilityFunctions::getObjectProperties($test);
        $testData['created_on'] = time();
        unset($testData['test_id']);

        $newId = $testTable->addRecord($testData);

        foreach($questions as $question)
        {
            $oldQuestionId=$question['test_question_id'];
            $question['test_id']= $newId;
            unset($question['test_question_id']);
            $questionId=  $testQuestionTable->addRecord($question);
            foreach($options[$oldQuestionId] as $option){
                $option['test_question_id'] = $questionId;
                unset($option['test_option_id']);
                $testOptionTable->addRecord($option);
            }

        }

        $this->flashMessenger()->addMessage(__('Test duplicated'));
        $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'index']);



    }


    public function resultsAction()
    {
        $testTable = new TestTable($this->getServiceLocator());
        $table = new StudentTestTable($this->getServiceLocator());
        $id = $this->params('id');

        $filter = $this->params()->fromQuery('filter', null);
        $startDate = $this->params()->fromQuery('start', null) ? strtotime($this->params()->fromQuery('start', null)):null;
        $endDate = $this->params()->fromQuery('end', null) ? strtotime($this->params()->fromQuery('end', null)):null ;

        if (empty($filter)) {
            $filter=null;
        }


        $row= $testTable->getRecord($id);
        $paginator = $table->getPaginatedRecords(true,$id,$filter,$startDate,$endDate);

        $testTotal = $table->getTotalForTest($id,$startDate,$endDate);
        $totalPassed = $table->getTotalPassed($id,$row->passmark,$startDate,$endDate);
        $totalFailed= $testTotal - $totalPassed;
        $average = $table->getAverageScore($id,$startDate,$endDate);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Test results').': '.$row->name,
            'row'=>$row,
            'passed'=>$totalPassed,
            'failed'=>$totalFailed,
            'average'=>$average,
            'start'=>$this->params()->fromQuery('start', null),
            'end'=>$this->params()->fromQuery('end', null)
        ));
    }

    public function deleteresultAction()
    {
        $studentTestTable = new StudentTestTable($this->getServiceLocator());

        $id = $this->params('id');
        $row = $studentTestTable->getRecord($id);
        $testId = $row->test_id;
        try{
            $studentTestTable->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'test','action'=>'results','id'=>$testId));
    }

    public function testresultAction()
    {
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $studentOptionTable = new StudentTestOptionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $studentTestTable->getRecord($id);
        $rowset = $studentOptionTable->getTestRecords($id);
        $data = ['row'=>$row,'rowset'=>$rowset];
        $viewModel = new ViewModel($data);
        $viewModel->setTerminal(true);
        return $viewModel;

    }

    public function exportresultAction(){

        $type = $_GET['type'];
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $testTable = new TestTable($this->getServiceLocator());
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
            $totalRecords = $studentTestTable->getTotalPassedForTest($id,$testRow->passmark,$startDate,$endDate);
        }
        else{
            $totalRecords = $studentTestTable->getTotalFailedForTest($id,$testRow->passmark,$startDate,$endDate);
        }

        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);
          fputcsv($myfile, array(__('First Name'),__('Last Name'),__('Score').'%'));
        for($i=1;$i<=$totalPages;$i++){
            if($type=='pass') {
                $paginator = $studentTestTable->getPassedPaginatedRecords(true, $id,$testRow->passmark,$startDate,$endDate);
            }
            else{
                $paginator = $studentTestTable->getFailPaginatedRecords(true, $id,$testRow->passmark,$startDate,$endDate);
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
        $sessionTestTable = new SessionTestTable($this->getServiceLocator());
        $testTable = new  TestTable($this->getServiceLocator());
        $testRow = $testTable->getRecord($id);

        $rowset = $sessionTestTable->getTestRecords($id);
        return [
          'pageTitle'=>__('test-sessions-courses').': '.$testRow->name,
            'rowset'=>$rowset,
            'id'=>$id
        ];

    }

    public function addsessionAction(){
        $id = $this->params('id');
        $sessionTestTable = new SessionTestTable($this->getServiceLocator());
        $testTable = new TestTable($this->getServiceLocator());
        $testRow = $testTable->getRecord($id);
        $form = $this->getSessionTestForm();
         $output = [];
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){

                $data = $form->getData();
                $data['test_id'] = $id;
                $data['opening_date']= strtotime($data['opening_date']);
                $data['closing_date']=strtotime($data['closing_date']);
                $sessionTestTable->addRecord($data);
                $this->flashMessenger()->addMessage(__('course-added-succ'));
                $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'sessions','id'=>$id]);


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
            $this->url()->fromRoute('admin/default',['controller'=>'test','action'=>'index'])=>__('Tests'),
            $this->url()->fromRoute('admin/default',['controller'=>'test','action'=>'sessions','id'=>$id])=>__('Sessions/Courses'),
            '#'=>__('add').' '.__('sessions-courses')
        ];
        return $output;
    }

    public function editsessionAction(){
        $id = $this->params('id');
        $sessionTestTable = new SessionTestTable($this->getServiceLocator());
        $row = $sessionTestTable->getRecord($id);
        $testTable = new TestTable($this->getServiceLocator());
        $testRow = $testTable->getRecord($row->test_id);
        $form = $this->getSessionTestForm();
        $output = [];
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){

                $data = $form->getData();

                $data['opening_date']= strtotime($data['opening_date']);
                $data['closing_date']=strtotime($data['closing_date']);
                $sessionTestTable->update($data,$id);
                $this->flashMessenger()->addMessage(__('course-saved'));
                $this->redirect()->toRoute('admin/default',['controller'=>'test','action'=>'sessions','id'=>$testRow->test_id]);


            }
            else{
                $output['message']= $this->getFormErrors($form);
            }
        }
        else{
            $data = UtilityFunctions::getObjectProperties($row);
            if(!empty($data['opening_date']))
                $data['opening_date'] = date('Y-m-d',$row->opening_date);

            if(!empty($data['closing_date']))
                $data['closing_date'] = date('Y-m-d',$row->closing_date);

            $form->setData($data);

        }

        $output['form'] = $form;
        $output['pageTitle'] = __('edit-course-for').' '.$testRow->name;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'test','action'=>'index'])=>__('Tests'),
            $this->url()->fromRoute('admin/default',['controller'=>'test','action'=>'sessions','id'=>$id])=>__('Sessions/Courses'),
            '#'=>__('edit').' '.__('sessions-courses')
        ];
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/test/addsession');
        return $viewModel;
    }

    public function deletesessionAction(){
       $id = $this->params('id');
        $testTable = new TestTable($this->getServiceLocator());
        $sessionTestTable= new SessionTestTable($this->getServiceLocator());
        $row = $sessionTestTable->getRecord($id);
        $testRow = $testTable->getRecord($row->test_id);
        if($testRow->account_id==$this->getAdminId() || GLOBAL_ACCESS){
            $sessionTestTable->deleteRecord($id);
            $this->flashMessenger()->addMessage(__('Record deleted'));
        }
        else{
            $this->flashMessenger()->addMessage(__('no-permission'));
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

        $form->createSelect('session_id', 'Session/Course', $options);
        $form->get('session_id')->setAttribute('class','form-control select2');


        $form->createText('opening_date','Opening Date (Optional)',false,'form-control date',null,'Opening Date');
        $form->createText('closing_date','Closing Date (Optional)',false,'form-control date',null,'Closing Date');

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

        $filter->add([
           'name'=>'opening_date',
            'required'=>false
        ]);

        $filter->add([
            'name'=>'closing_date',
            'required'=>false
        ]);

        return $filter;

    }


    public function importquestionsAction(){

        $id = $this->params('id');

        $testQuestionTable = new TestQuestionTable();

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
                $correctOption = intval($value['Correct_Option_Number']);


                //create new question
                $testQuestion = new TestQuestion();
                $testQuestion->question = trim($question);
                if(!empty($lastSortOrder)){
                    $lastSortOrder++;
                    $testQuestion->sort_order = $lastSortOrder;
                }
                else{
                    $testQuestion->sort_order = 0 ;
                }

                $testQuestion->test_id = $id;
                $testQuestion->save();
                $imported++;
                //get options
                $optionEntries= explode(',',$options);
                $count =0;
                foreach ($optionEntries as $optionValue){

                    if(!empty($optionValue)){
                        $count++;
                        $testOption=new TestOption();
                        $testOption->test_question_id= $testQuestion->test_question_id;
                        $testOption->option = trim($optionValue);
                        if($count == $correctOption){
                            $testOption->is_correct = 1;
                        }
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
        $test =  Test::find($id);

        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die(__('unable-to-open'));

        $columns = array(__('Question'),__('Options'),__('correct-option-number'));
        fputcsv($myfile,$columns);

        foreach ($test->testQuestions()->orderBy('sort_order')->get() as $testQuestion){

            $data = [];
            $data[0] = strip_tags($testQuestion->question);

            $optionCount = 0;
            $correct = 0;
            $optionArray = [];
            foreach ($testQuestion->testOptions as $testOption){
                $optionCount++;
                $optionArray[] = $testOption->option;
                if($testOption->is_correct==1){
                    $correct = $optionCount;
                }
            }
            $data[1] = implode(',',$optionArray);
            $data[2] = $correct;
            fputcsv($myfile,$data);
        }

        fclose($myfile);
        header('Content-type: text/csv');
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.safeUrl($test->name).'_questions_'.date('d/M/Y').'.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();

    }
}