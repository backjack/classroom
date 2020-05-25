<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/16/2017
 * Time: 12:46 PM
 */

namespace Application\Controller;


use Application\Model\AssignmentSubmissionTable;
use Application\Model\AssignmentTable;
use Application\Model\StudentSessionTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Element\File;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class AssignmentController extends AbstractController {

    use HelperTrait;

    protected $uploadDir;

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/student');
        }, 100);
    }


    public function __construct(){
        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $this->uploadDir = 'usermedia'.$user.'/student_uploads/'.date('Y_m');



    }

    private function makeUploadDir(){
        $path = 'public/'.$this->uploadDir;
        if(!file_exists($path)){
            mkdir($path,0777,true);
        }
    }

    public function indexAction(){

        $studentId = $this->getId();
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $submissionTable = new AssignmentSubmissionTable($this->getServiceLocator());

        $paginator = $studentSessionTable->getAssignments($studentId);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        return [
            'pageTitle'=>__('Homework'),
            'paginator'=>$paginator,
            'submissionTable' => $submissionTable,
            'total'=> $studentSessionTable->getTotalAssignments($studentId)
        ];
    }

    private function validateAssignment($id){
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $assignmentRow = $assignmentTable->getRecord($id);

        if( $assignmentRow->allow_late!=1 && $assignmentRow->due_date < time()){
            $this->flashMessenger()->addMessage(__('ass-past-due-date'));
            $this->goBack();
        }

        if(!$studentSessionTable->enrolled($this->getId(),$assignmentRow->session_id)){
            $this->flashMessenger()->addMessage(__('you-not-enrolled'));
            $this->goBack();
        }

    }

    public function submitAction(){
        $sessionContainer = new Container('course');

        $output = [];
        $id = $this->params('id');
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $assignmentRow = $assignmentTable->getRecord($id);
        $form = $this->getForm($id);

        $this->validateAssignment($id);

        //check if student has submitted assignment
        if($assignmentSubmissionTable->hasSubmission($this->getId(),$id)){

            $submissionRow = $assignmentSubmissionTable->getAssignment($id,$this->getId());
            if($this->canEdit($submissionRow->assignment_submission_id)){
                $this->flashMessenger()->addMessage(__('already-submitted-msg'));
                $this->redirect()->toRoute('application/edit-assignment',['id'=>$submissionRow->assignment_submission_id]);

            }
            else{
                $this->flashMessenger()->addMessage(__('ass-no-edit-msg'));
                $this->goBack();
            }

        }


        if($this->request->isPost()){

            $formData = $this->request->getPost();


            $form->setData(array_merge_recursive(
                $formData->toArray(),
                $this->request->getFiles()->toArray()
            ));
            if($form->isValid()){
                $data = $form->getData();

                //handle file upload
                if($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b'){
                    $file = $data['file_path']['name'];
                    $newPath = $this->uploadDir.'/'.time().$this->getId().'_'.sanitize($file);
                    $this->makeUploadDir();
                    rename($data['file_path']['tmp_name'],'public/'.$newPath);

                    try{
                        chmod('public/'.$newPath,644);
                    }
                    catch(\Exception $ex){

                    }


                    $data['file_path'] = $newPath;
                }
                $content = $this->saveInlineImages($data['content'],$this->getBaseUrl());

                $data['content'] = sanitizeHtml($content);
                $data['student_id'] = $this->getId();
                $data['assignment_id'] = $id;
                $data['created'] = time();
                $data['modified'] = time();
                $data['editable'] = 1;

                $aid = $assignmentSubmissionTable->addRecord($data);
                if($data['submitted']==1 && !empty($aid)){
                    $student = $this->getStudent();
                    $message = $student->first_name.' '.$student->last_name.' '.__('just-submitted-msg').' "'.$assignmentRow->title.'"';
                    $this->notifyAdmin($assignmentRow->account_id,__('New homework submission'),$message);
                }
                $this->flashMessenger()->addMessage(__('you-successfully-submitted'));
                if(!empty($sessionContainer->url))
                {
                    $url = $sessionContainer->url;
                    $sessionContainer->url=null;
                    return $this->redirect()->toUrl($url);
                }

                $this->redirect()->toRoute('application/assignment-submissions');

            }
            else{
                $output['message'] = $this->getFormErrors($form);
            }



        }

        return array_merge([
            'pageTitle'=>__('Submit Homework').': '.$assignmentRow->title,
            'form'=>$form,
            'row'=>$assignmentRow
        ],$output);

    }

    public function submissionsAction(){
        $studentId = $this->getId();
        $assignmentSubmissionsTable = new AssignmentSubmissionTable($this->getServiceLocator());

        $paginator = $assignmentSubmissionsTable->getStudentPaginatedRecords(true,$studentId);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        return [
            'pageTitle'=>__('My Homework Submissions'),
            'paginator'=>$paginator,
        ];
    }

    private function canEdit($id){
        $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $assignmentTable = new AssignmentTable($this->getServiceLocator());

        //get assignment
        $row = $assignmentSubmissionTable->getRecord($id);
        $assignmentRow = $assignmentTable->getRecord($row->assignment_id);
        if(empty($row->editable) || ( $assignmentRow->allow_late!=1 && $assignmentRow->due_date < time())){
            return false;
        }
        else{
            return true;
        }

    }

    public function editAction(){

        $id = $this->params('id');
        $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $assignmentTable = new AssignmentTable($this->getServiceLocator());

        //get assignment
        $row = $assignmentSubmissionTable->getRecord($id);
        $assignmentRow = $assignmentTable->getRecord($row->assignment_id);
       if(!$this->canEdit($id)){
           $this->flashMessenger()->addMessage(__('sorry-no-edit'));
           $this->goBack();
       }

        $form = $this->getForm($assignmentRow->assignment_id,false);
        $form->get('file_path')->setAttribute('required','');

        if($this->request->isPost()){

            $formData = $this->request->getPost();

            $form->setData(array_merge_recursive(
                $formData->toArray(),
                $this->request->getFiles()->toArray()
            ));

            if($form->isValid()){
                $data = $form->getData();

                //handle file upload
                if(($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b') && !empty($data['file_path']['name'])){
                    //remove old file
                    @unlink($row->file_path);
                    $file = $data['file_path']['name'];
                    $newPath = $this->uploadDir.'/'.time().$this->getId().'_'.sanitize($file);
                    $this->makeUploadDir();
                    rename($data['file_path']['tmp_name'],'public/'.$newPath);
                    $data['file_path'] = $newPath;

                }
                else{
                    unset($data['file_path']);
                }

                $data['modified'] = time();
                $assignmentSubmissionTable->update($data,$id);
                $this->flashMessenger()->addMessage(__('you-successfully-edited'));
                $this->redirect()->toRoute('application/assignment-submissions');

            }
            else{
                    $message = $this->getFormErrors($form);
            }

        }
        else{
            $data = UtilityFunctions::getObjectProperties($row);
            $form->setData($data);

        }
        $pageTitle = __('Edit Assignment').': '.$assignmentRow->title;


        $data= compact('pageTitle','message','assignmentRow','form');
        $data['row'] = $assignmentRow;
        if(!empty($row->file_path)){
            $data['file']  = basename($row->file_path);
        }
        $viewModel = new ViewModel($data);
        $viewModel->setTemplate('application/assignment/submit');
        return $viewModel;

    }

    public function deleteAction(){
        $id = $this->params('id');
        $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $assignmentTable = new AssignmentTable($this->getServiceLocator());

        //get assignment
        $row = $assignmentSubmissionTable->getRecord($id);
        $assignmentRow = $assignmentTable->getRecord($row->assignment_id);
        if(empty($row->editable) || ( $assignmentRow->allow_late!=1 && $assignmentRow->due_date < time())){
            $this->flashMessenger()->addMessage(__('sorry-no-delete'));
            return $this->goBack();
            exit();
        }

        $this->validateOwner($row);

        if(!empty($row->file_path)){
            @unlink($row->file_path);
        }

        $assignmentSubmissionTable->deleteRecord($id);
        $this->flashMessenger()->addMessage(__('Assignment deleted'));
        $this->goBack();

    }

    public function viewAction(){
        $id = $this->params('id');
        $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
        $row = $assignmentSubmissionTable->getSubmission($id);
        $viewModel= new ViewModel(['row'=>$row]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    private function getForm($id,$fileRequired=true){
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $assignmentRow = $assignmentTable->getRecord($id);
        $form = new BaseForm();

        if($assignmentRow->assignment_type=='t' || $assignmentRow->assignment_type=='b'){
            $form->createTextArea('content','Your Answer',true);
            $form->get('content')->setAttribute('class','summernote form-control');

        }

        if($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b' ){
            $file = new File('file_path');
            $file->setLabel(__('Your File'))
                ->setAttribute('id','file_path')
                ->setAttribute('required','required');
            $form->add($file);
        }

        $form->createSelect('submitted','Status',['1'=>__('Submitted'),'0'=>__('Draft')],true,false);
        $form->createTextArea('student_comment','Additional Comments (optional)',false);

        $form->setInputFilter($this->getFilter($id,$fileRequired));
        return $form;

    }

    private function getFilter($id,$fileRequired){
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $assignmentRow = $assignmentTable->getRecord($id);
        $filter = new InputFilter();

        if($assignmentRow->assignment_type=='t' || $assignmentRow->assignment_type=='b'){
            $filter->add([
                'name'=>'content',
                'required'=>true,
                'validators'=>[
                    [
                        'name'=>'NotEmpty'
                    ]
                ]
            ]);
        }

        if($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b' ){

            $input = new Input('file_path');
            $input->setRequired($fileRequired);
            $input->getValidatorChain()
                ->attach(new Size(5000000))
                ->attach(new Extension('jpg,mp4,mp3,avi,xls,7z,mdb,mdbx,csv,xlsx,txt,zip,doc,docx,pptx,pdf,ppt,png,gif,jpeg'));

            $filter->add($input);
        }

        $filter->add([
            'name'=>'submitted',
            'required'=>false
        ]);

        $filter->add([
            'name'=>'student_comment',
            'required'=>false
        ]);

        return $filter;
    }




}