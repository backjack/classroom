<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/11/2017
 * Time: 2:34 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Model\AccountsTable;
use Application\Model\AssignmentSubmissionTable;
use Application\Model\AssignmentTable;
use Application\Model\HomeworkTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;
use Zend\Log\Formatter\Base;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AssignmentController extends AbstractController {
    use HelperTrait;

    public function indexAction(){
        $table = new AssignmentTable($this->getServiceLocator());
        $submissionTable = new AssignmentSubmissionTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Homework'),
            'submissionTable' => $submissionTable,
            'total' => $table->getTotalAdminAssignments($this->getAdminId())
        ));

    }

    public function addAction()
    {

        $output = array();
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $form = $this->getAssignmentForm();

        $filter = $this->getAssignmentFilter();

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();

            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();

                $array[$assignmentTable->getPrimary()]=0;
                $array['created_on']=time();
                $array['due_date'] = !empty($array['due_date'])?strtotime($array['due_date']):null;
                $array['opening_date'] = !empty($array['opening_date'])?strtotime($array['opening_date']):null;

                $array['account_id'] = $this->getAdminId();

                $assignmentTable->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                if(!empty($data['notify'])){
                    $subject = 'New Homework';
                     $message= __('new-homework-email',['title'=>$data['title'],'instruction'=>$data['instruction'],'due-date'=>$data['due_date']]);
                     $sms= __('new-homework-sms',['title'=>$data['title'],'due-date'=>$data['due_date']]);

                    $this->notifySessionStudents($data['session_id'],$subject,$message,true,$sms);
                }
                
                $this->flashMessenger()->addMessage('Record Added!');
                $this->redirect()->toRoute('admin/default',['controller'=>'assignment','action'=>'index']);
            }
            else{
                $output['message'] = __('save-failed-msg');
            }

        }



        $output['form'] = $form;
        $output['pageTitle']= __('Add Homework');
        $output['action']='add';
        $output['id']=null;
        return new ViewModel($output);
    }


    public function editAction(){
        $output = array();
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $form = $this->getAssignmentForm();
        $filter = $this->getAssignmentFilter();
        $id = $this->params('id');

        $row = $assignmentTable->getRecord($id);
        $oldName = $row->title;
        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();

            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();

                $array[$assignmentTable->getPrimary()]=$id;

                $array['due_date'] = !empty($array['due_date'])?strtotime($array['due_date']):null;
                $array['opening_date'] = !empty($array['opening_date'])?strtotime($array['opening_date']):null;

                $assignmentTable->saveRecord($array);

                if(!empty($data['notify_students'])){
                    $subject = __('New Homework');

                    $message= __('new-homework-email',['title'=>$data['title'],'instruction'=>$data['instruction'],'due-date'=>$data['due_date']]);
                    $textMessage= __('new-homework-sms',['title'=>$data['title'],'due-date'=>$data['due_date']]);


                    $this->notifySessionStudents($data['session_id'],$subject,$message,false,$textMessage);
                }

                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',['controller'=>'assignment','action'=>'index']);


            }
            else{

                $output['message'] = $this->getFormErrors($form);
            }

        }
        else {

            $data = UtilityFunctions::getObjectProperties($row);
            $data['due_date'] = !empty($data['due_date'])? date('Y-m-d',$data['due_date']):null;
            $data['opening_date'] = !empty($data['opening_date'])?date('Y-m-d',$data['opening_date']):null;
            $form->setData($data);

        }

        $output['form'] = $form;
        $output['id'] = $id;
        $output['pageTitle']= __('Edit Homework');
        $output['row']= $row;
        $output['action']='edit';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/assignment/add');
        return $viewModel ;

    }

    public function viewAction(){
        $id = $this->params('id');
        $homeworkTable = new AssignmentTable($this->getServiceLocator());
        $submissionsTable = new AssignmentSubmissionTable($this->getServiceLocator());

        $data =[
          'row' => $homeworkTable->getRecord($id),
            'table' => $submissionsTable,
            'accountsTable' => new AccountsTable($this->getServiceLocator())
        ];
        $viewModel = new ViewModel($data);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function deleteAction()
    {
        $table = new AssignmentTable($this->getServiceLocator());
        $id = $this->params('id');
        $table->deleteRecord($id);
        $this->flashmessenger()->addMessage(__('Record deleted'));
        $this->goBack();
    }

    public function submissionsAction()
    {
        $assignmentSubmissionsTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $id = $this->params('id');

        $assignmentRow = $assignmentTable->getRecord($id);

        $paginator = $assignmentSubmissionsTable->getAssignmentPaginatedRecords(true,$id);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        $assignmentTotal = $assignmentSubmissionsTable->getTotalSubmittedForAssignment($id);
        $totalPassed = $assignmentSubmissionsTable->getTotalPassedForAssignment($id,$assignmentRow->passmark);
        $totalFailed= $assignmentTotal - $totalPassed;
        $average = $assignmentSubmissionsTable->getAverageScore($id);


        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Homework Submissions:').' '.$assignmentRow->title,
            'total' => $assignmentTotal,
            'passed' => $totalPassed,
            'failed' => $totalFailed,
            'average'=>$average,
            'row'=>$assignmentRow
        ));


    }

    public function viewsubmissionAction(){
        $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $assignmentSubmissionTable->getSubmission($id);
        $form = $this->getGradeForm();

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();
                $assignmentSubmissionTable->update($data,$id);


                if(!empty($formData['notify'])){
                    $this->notifyStudent($row->student_id,__('homework-graded-mail-title'),__('homework-graded-mail-msg',['title'=>$row->title]));
                }

                $this->flashMessenger()->addMessage('You have successfully updated this assignment');
                $this->redirect()->toRoute('admin/default',['controller'=>'assignment','action'=>'submissions','id'=>$row->assignment_id]);
            }
            else{
                $this->data['message'] = $this->getFormErrors($form);
            }

        }
        else{
            $form->setData(UtilityFunctions::getObjectProperties($row));
            if(empty($row->grade)){
                $form->get('editable')->setValue(0);
            }

        }

        $this->data['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'assignment','action'=>'index'])=>__('Homework'),
            $this->url()->fromRoute('admin/default',['controller'=>'assignment','action'=>'submissions','id'=>$row->assignment_id])=>__('Submissions'),
            '#'=>__('View Submission')
        ];

        return new ViewModel (array_merge(array(
            'row'=>$row,
            'pageTitle'=>__('Homework Submission:').' '.$row->title,
            'form'=>$form
        ),$this->data));
    }

    public function exportresultAction(){

        $type = $_GET['type'];
        $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die("Unable to open file!");
        $id = $this->params('id');
        $assignmentRow = $assignmentTable->getRecord($id);
        if($type=='pass')
        {
            $totalRecords = $assignmentSubmissionTable->getTotalPassedForAssignment($id,$assignmentRow->passmark);
        }
        else{
            $totalRecords = $assignmentSubmissionTable->getTotalFailedForAssignment($id,$assignmentRow->passmark);
        }

        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);
        fputcsv($myfile, array(__('First Name'),__('Last Name'),__('Email'),__('score').' %'));
        for($i=1;$i<=$totalPages;$i++){
            if($type=='pass') {
                $paginator = $assignmentSubmissionTable->getPassedPaginatedRecords(true, $id,$assignmentRow->passmark);
            }
            else{
                $paginator = $assignmentSubmissionTable->getFailPaginatedRecords(true, $id,$assignmentRow->passmark);
            }

            $paginator->setCurrentPageNumber($i);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row){

                fputcsv($myfile, array($row->first_name,$row->last_name,$row->email,$row->grade));

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

    public function sessionlessonsAction(){

        $id = $this->params('id');
        $selected = $this->params()->fromQuery('lesson_id');

        $select = new Select('lesson_id');
        $select->setEmptyOption('--'.__('select').'--');
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $rowset= $sessionLessonTable->getSessionRecords($id);

        $options = [];
        foreach($rowset as $row){
            $options[$row->lesson_id]= $row->lesson_name;
        }
        $select->setLabel('Class');
        $select->setAttribute('class','form-control');
        $select->setValueOptions($options);

        if($selected){
            $select->setValue($selected);
        }
        $viewModel = new ViewModel(['select'=>$select]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    private function getGradeForm(){
        $form= new BaseForm();
        $form->createTextArea('admin_comment',__('comment').' ('.__('optional').')',false);
        $form->createText('grade',__('grade').' (%)',true,'form-control digit',null,__('digits-only'));
        $form->createSelect('editable',__('Status'),['0'=>__('graded').' ('.__('un-editable').')','1'=>__('ungraded').' ('.__('editable').')'],true,false);
        $form->setInputFilter($this->getGradeFilter());
        return $form;
    }

    private function getGradeFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'admin_comment',
            'required'=>'false'
        ]);
        $filter->add([
            'name'=>'grade',
            'required'=>'true',
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ],
                [
                    'name'=>'Digits'
                ]
            ]
        ]);
        $filter->add([
            'name'=>'editable',
            'required'=> true
        ]);
        return $filter;
    }

    private function getAssignmentForm(){
        $form = new BaseForm();
        $sessionTable = new SessionTable($this->getServiceLocator());
        $rowset = $sessionTable->getLimitedRecords(5000);




        $options = [];
        $log = [];
        foreach($rowset as $row){
           // $options[$row->session_id] = $row->session_name;
            $options[] =  ['attributes'=>['data-type'=>$row->session_type],'value'=>$row->session_id,'label'=>$row->session_name.' ('.$row->session_id.')'];
            $log[$row->session_id]=true;
        }
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $rowset = $sessionInstructorTable->getAccountRecords($this->getAdminId());
        foreach($rowset as $row){
            if($log[$row->session_id]){
                continue;
            }
           // $options[$row->session_id] = $row->session_name;
            $options[] =  ['attributes'=>['data-type'=>$row->session_type],'value'=>$row->session_id,'label'=>$row->session_name.' ('.$row->session_id.')'];

        }

        //$form->createSelect('session_id','Session/Course',$options,true);
       // $form->get('session_id')->setAttribute('class','form-control select2');

        $sessionId = new Select('session_id');
        $sessionId->setLabel(__('Session/Course'));
        $sessionId->setAttribute('class','form-control select2');
        $sessionId->setAttribute('id','session_id');
        $sessionId->setValueOptions($options);

        $form->add($sessionId);


        $form->createText('due_date','Due Date',true,'form-control date');
        $form->createText('opening_date','Opening Date',true,'form-control date');
        $form->createSelect('schedule_type','Type',['s'=>__('Scheduled'),'c'=>__('Post Class')],true,false);
        $form->createText('title','Title',true);
        $form->createSelect('assignment_type','Student Response Type',['t'=>__('Text'),'f'=>__('File Upload'),'b'=>__('Text & File Upload')],true);
        $form->createTextArea('instruction','Homework Instructions',true);
        $form->get('instruction')->setAttribute('id','instruction');
        $form->createHidden('lesson_id');

        $form->createText('passmark','Passmark (%)',true,'number form-control');
        $form->createCheckbox('notify','Receive submission notifications?',1);
        $form->createCheckbox('allow_late','Allow late submissions?',1);
        return $form;
    }

    private function getAssignmentFilter(){
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
            'name'=>'due_date',
            'required'=>false,

        ]);


        $filter->add([
            'name'=>'assignment_type',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'instruction',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'passmark',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'notify',
            'required'=>false,
           
        ]);

        $filter->add([
            'name'=>'allow_late',
            'required'=>false,

        ]);

        $filter->add([
            'name'=>'opening_date',
            'required'=>false,

        ]);

        $filter->add([
            'name'=>'lesson_id',
            'required'=>false,

        ]);



        $filter->add([
            'name'=>'schedule_type',
            'required'=>true,

        ]);

        $filter->add([
            'name'=>'title',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        return $filter;
    }

}