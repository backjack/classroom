<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2017
 * Time: 4:06 PM
 */

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Entity\Certificate;
use Application\Entity\Lesson;
use Application\Entity\Session;
use Application\Entity\Test;
use Application\Form\CertificateFilter;
use Application\Form\CertificateForm;
use Application\Form\EditCertificateForm;
use Application\Model\AssignmentTable;
use Application\Model\CertificateAssignmentTable;
use Application\Model\CertificateFieldTable;
use Application\Model\CertificateFieldTypeTable;
use Application\Model\CertificateLessonTable;
use Application\Model\CertificateSessionTable;
use Application\Model\CertificateTable;
use Application\Model\CertificateTestTable;
use Application\Model\LessonTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\StudentCertificateTable;
use Application\Model\StudentSessionTable;
use Application\Model\TestTable;
use Intermatics\UtilityFunctions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
define('DIR_MER_IMAGE', 'public/');
class CertificateController extends AbstractController {

    public function indexAction(){
        $table = new CertificateTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Certificates'),
            'articleTable'=>$table
        ));
    }



    public function addAction()
    {
        $output = array();
        $lessonTable = new CertificateTable($this->getServiceLocator());
        $form = new CertificateForm(null,$this->getServiceLocator());
        $filter = new CertificateFilter();

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                $array[$lessonTable->getPrimary()]=0;
                $array['created_on']=time();
                $id= $lessonTable->saveRecord($array);
                   $this->flashmessenger()->addMessage(__('record-added!'));

                $this->redirect()->toRoute('admin/default',['controller'=>'certificate','action'=>'edit','id'=>$id]);


            }
            else{
                $output['message'] = __('save-failed-msg');
                if ($data['certificate_image']) {
                    $output['display_image']= resizeImage($data['picture'], 100, 100,$this->getBaseUrl());
                }
            }

        }
        else{
            $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
            $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }
        $output['form'] = $form;
        $output['pageTitle']= __('Add Certificate');
        $output['action']='add';
        $output['id']=null;
        return new ViewModel($output);
    }



    public function editAction(){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $testTable = new TestTable($this->getServiceLocator());
        $assignmentTable = new AssignmentTable();
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $certificateLessonTable = new CertificateLessonTable($this->getServiceLocator());
        $certificateTestTable = new CertificateTestTable($this->getServiceLocator());
        $certificateAssignmentTable = new CertificateAssignmentTable();
        $id = $this->params('id');
        $form = new EditCertificateForm(null,$this->getServiceLocator(),$id);
        $filter = new CertificateFilter();
        $output= [];


        //get session list


        $form->setInputFilter($filter);
        if($this->request->isPost())
        {
            $formData = $this->request->getPost();

            $form->setData($formData);

            if($form->isValid()){
                $data= $form->getData();
               $newSrc = $this->getBaseUrl().'/'.$data['certificate_image'];

                $certificateRow = $certificateTable->getRecord($id);

                $html = $data['html'];

                if(empty($html)){
                    $html = $certificateRow->html;
                }

                if(!empty($html)){
                    $dom = new \DOMDocument();
                    $dom->loadHTML($html);

                    $new_elm = $dom->createElement('style', '* { font-family: DejaVu Sans, sans-serif; }');
                    $elm_type_attr = $dom->createAttribute('type');
                    $elm_type_attr->value = 'text/css';
                    $new_elm->appendChild($elm_type_attr);

                    $head = $dom->createElement('head');

                    $head->appendChild($new_elm);

                    $html = $dom->getElementsByTagName('html')->item(0);
                    $body = $dom->getElementsByTagName('body')->item(0);
                    $html->insertBefore($head,$body);


                    foreach ($dom->getElementsByTagName('img') as $img) {
                        // put your replacement code here
                        $img->setAttribute( 'src', $newSrc );
                    }

                    $data['html'] = $dom->saveHTML();

                }




                if($certificateRow->session_id != $data['session_id']){
                    $data['html']='';
                }



                //remove lesson records
                foreach($data as $key=>$value){
                   if(preg_match('#lesson_#',$key)){
                       unset($data[$key]);
                   }
                }

                $certificateTable->update($data,$id);

                //now save classes
                $certificateLessonTable->clearCertificateRecords($id);


                foreach($formData as $key=>$value){
                    if(preg_match('#lesson_#',$key) && !empty($value)){
                        $certificateLessonTable->addRecord([
                           'certificate_id'=>$id,
                            'lesson_id'=>$value
                        ]);
                    }
                }

                $certificateTestTable->clearCertificateRecords($id);
                foreach($formData as $key=>$value){
                    if(preg_match('#test_#',$key) && !empty($value)){
                        $certificateTestTable->addRecord([
                            'certificate_id'=>$id,
                            'test_id'=>$value
                        ]);
                    }
                }
                
                $certificateAssignmentTable->clearCertificateRecords($id);
                   foreach($formData as $key=>$value){
                    if(preg_match('#assignment_#',$key) && !empty($value)){
                        $certificateAssignmentTable->addRecord([
                            'certificate_id'=>$id,
                            'assignment_id'=>$value
                        ]);
                    }
                }

                $output['message'] = __('Changes Saved!');
                $row = $certificateTable->getRecord($id);
            }
            else{
                $output['message']=__('save-failed-msg');
            }


            $form->populateValues($formData);
        }	
        else {


            $row = $certificateTable->getRecord($id);



            $data = UtilityFunctions::getObjectProperties($row);

            $lessons = $certificateLessonTable->getCertificateLessons($id);
            foreach($lessons as $row2){
                $data['lesson_'.$row2->lesson_id]=$row2->lesson_id;
            }

            $form->setData($data);

        }
        $row = $certificateTable->getRecord($id);

        //add lesson records to form here
        $rowset = $sessionLessonTable->getSessionRecords($row->session_id);
         $form->translate = false;
        foreach($rowset as $row2){
            $form->createCheckbox('lesson_'.$row2->lesson_id,$row2->lesson_name,$row2->lesson_id);
            if($certificateLessonTable->hasLesson($id,$row2->lesson_id))
            {
                $form->get('lesson_'.$row2->lesson_id)->setValue($row2->lesson_id);
            }

        }
        $form->translate = true;


        //get lessons for session
        $lessons = $sessionLessonTable->getSessionRecords($row->session_id);

        //get tests for session
        $tests = $certificateTestTable->getCertificateRecords($id);
        $assignments = $certificateAssignmentTable->getCertificateRecords($id);

        if($row->orientation=='p'){
            $width = 595;
            $height = 842;
        }
        else{
            $width = 842;
            $height = 595;
        }

        $output['allTests'] = $this->getSessionTestsObjects($row->session_id);
        $output['allAssignments'] = $assignmentTable->getPaginatedRecords(false, $row->session_id);
        

        
        $output = array_merge($output,[
            'row'=>$row,
            'pageTitle'=>__('Edit Certificate').': '.$row->certificate_name,
            'certificateLessonTable'=>$certificateLessonTable,
            'lessons'=>$lessons,
            'tests'=>$tests,
            'assignments'=>$assignments,
            'width'=>$width,
            'height'=>$height,
            'siteUrl'=>$this->getBaseUrl(),
            'form' => $form,
            'action'=>'edit',
            'id'=>$id
        ]);



        if ($row->certificate_image && file_exists(DIR_MER_IMAGE . $row->certificate_image) && is_file(DIR_MER_IMAGE . $row->certificate_image)) {
            $output['display_image'] = resizeImage($row->certificate_image, 100, 100,$this->getBaseUrl());
        } else {
            $output['display_image'] = resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }


        $output['no_image']= $this->getBaseUrl().'/'.resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());


        return $output;
    }

    public function fixAction(){


        $certificates = Certificate::get();
        foreach($certificates as $certificateRow){
            $newSrc = $this->getBaseUrl().'/'.$certificateRow->certificate_image;

            $html = $certificateRow->html;
            if(empty($html)){
                continue;
            }
            $dom = new \DOMDocument();
            $dom->loadHTML($html);

            foreach ($dom->getElementsByTagName('img') as $img) {
                // put your replacement code here
                $img->setAttribute( 'src', $newSrc );
            }

            $certificateRow->html = $dom->saveHTML();
            $certificateRow->save();


        }

        exit('Fixed all');

    }

    public function resetAction()
    {
        $id = $this->params('id');
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $certificateTable->update(['html'=>''],$id);
        $this->flashMessenger()->addMessage(__('certificate-reset'));
        $this->redirect()->toRoute('admin/default',['controller'=>'certificate','action'=>'edit','id'=>$id]);

    }

    public function loadclassesAction(){
        $id = $this->params('id');

        $sessionLessonTable= new SessionLessonTable($this->getServiceLocator());

        $rowset = $sessionLessonTable->getSessionRecords($id);

        $viewModel = new ViewModel(['rowset'=>$rowset]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function getBaseUrl()
    {
        $event = $this->getEvent();
        $request = $event->getRequest();
        $router = $event->getRouter();
        $uri = $router->getRequestUri();
        $baseUrl = sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $request->getBaseUrl());
        return $baseUrl;
    }

    public function deleteAction(){
        $id= $this->params('id');
        $table = new CertificateTable($this->getServiceLocator());
        $table->deleteRecord($id);
        $this->flashMessenger()->addMessage(__('Record deleted'));
        $this->redirect()->toRoute('admin/default',['controller'=>'certificate','action'=>'index']);

    }

    public function duplicateAction(){

        $id = $this->params('id');

        //get tables
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $certificateLessonTable = new CertificateLessonTable($this->getServiceLocator());
        $certificateTestTable = new CertificateTestTable($this->getServiceLocator());

        //now get session records
        $certiciateRow = $certificateTable->getRecord($id);
        $certificateLessonRowset = $certificateLessonTable->getCertificateLessons($id);
        $certificateTestRowset = $certificateTestTable->getCertificateTests($id);

        //create row
        $certificateArray= UtilityFunctions::getObjectProperties($certiciateRow);
        unset($certificateArray['certificate_id']);
        $newId = $certificateTable->addRecord($certificateArray);

        //now get lessons
        foreach($certificateLessonRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);

            $data['certificate_id']=$newId;
            $certificateLessonTable->addRecord($data);
        }

        //get instructors
        foreach($certificateTestRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);

            $data['certificate_id']=$newId;
            $certificateTestTable->addRecord($data);
        }

        $this->flashMessenger()->addMessage(__('certificate-duplicated'));
        $this->redirect()->toRoute('admin/default',array('controller'=>'certificate','action'=>'index'));


    }


    public function studentsAction(){
        $id = $this->params('id');
        $certificate= Certificate::find($id);
        $studentCertificates= $certificate->studentCertificates()->orderBy('student_certificate_id','desc')->paginate(30);
        $total = $certificate->studentCertificates()->count();

        return $this->bladeView('admin.certificate.students',['students'=>$studentCertificates,'total'=>$total,'pageTitle'=>__('Student Certificates').': '.$certificate->certificate_name.' ('.$total.')']);

    }


    public function trackAction(){

        $studentSessionTable= new StudentCertificateTable();
        $filter = $this->request->getQuery('query');

        if(!empty($filter)){
            $paginator = $studentSessionTable->searchStudents($filter);

            $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
            $paginator->setItemCountPerPage(30);
        }
   else{
            $paginator = false;
   }

        return $this->bladeView('admin.certificate.track',['paginator'=>$paginator,'pageTitle'=>__('Track Certificate')]);

    }

    
    private function getSessionTests($sessionId){
        $session = Session::find($sessionId);
        //create list of tests for this session
        $allTests = [];
        foreach($session->sessionTests as $test){
            $allTests[$test->test_id] = $test->test_id;
        }

        foreach($session->sessionLessons as $sessionLesson){
            $lesson = Lesson::find($sessionLesson->lesson_id);

            if( $lesson && !empty($lesson->test_id) && !empty($lesson->test_required) && Test::find($lesson->test_id)){
                $allTests[$lesson->test_id] = $lesson->test_id;
            }

        }
        return $allTests;
    }

    private function getSessionTestsObjects($sessionId){
        $testIds = $this->getSessionTests($sessionId);
        $objects = [];
      foreach($testIds as $id)
      {
          $test = Test::find($id);
          if($test){
              $objects[] = $test;
          }
      }
        return $objects;
    }


}