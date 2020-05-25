<?php
namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Entity\Invoice;
use Application\Entity\InvoiceTransaction;
use Application\Entity\Student;
use Application\Entity\StudentSession;
use Application\Form\LessonFilter;
use Application\Form\LessonForm;
use Application\Form\SessionFilter;
use Application\Form\SessionForm;
use Application\Model\AccountsTable;
use Application\Model\AttendanceTable;
use Application\Model\InvoiceTable;
use Application\Model\InvoiceTransactionTable;
use Application\Model\LessonGroupTable;
use Application\Model\LessonTable;
use Application\Model\LessonToLessonGroupTable;
use Application\Model\MaritalStatusTable;
use Application\Model\OldAttendanceTable;
use Application\Model\OldAttendeeSessionTable;
use Application\Model\OldAttendeeTable;
use Application\Model\OldSessionTable;
use Application\Model\PaymentTable;
use Application\Model\RegistrationFieldTable;
use Application\Model\SessionCategoryTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionLessonAccountTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\SessionToSessionCategoryTable;
use Application\Model\SettingTable;
use Application\Model\StudentFieldTable;
use Application\Model\StudentSessionTable;
use Application\Model\TransactionTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\Opencart\Library\Mail;
use Zend\Code\Scanner\Util;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\IsImage;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Application\Model\StudentCategoriesTable;
use Intermatics\UtilityFunctions;
use Application\Model\StudentTable;
use Application\Form\StudentForm;
use Application\Form\StudentFilter;

/**
 * ParentsController
 *
 * @author
 *
 * @version
 *
 */
define('DIR_MER_IMAGE', 'public/');
class StudentController extends AbstractController
{
    use HelperTrait;

    protected $acceptCriteria = array(
        'Zend\View\Model\ViewModel' => array(
            'text/html',
        ),
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ));
    protected $uploadDir;

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

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        // TODO Auto-generated ParentsController::indexAction() default action

        $table = new StudentTable($this->getServiceLocator());
        $filter = $this->params()->fromQuery('filter', null);
         
       
        if (empty($filter)) {
        	$filter=null;
        }
         
        
        $text = new Text('filter');
        $text->setAttribute('class','form-control');
        $text->setAttribute('placeholder',__('filter-name-email'));
        $text->setValue($filter);

        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        
        $paginator = $table->getStudents(true,$filter);
        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
        		'paginator'=>$paginator,
        		'pageTitle'=>__('Students').': '.$table->getTotal(),
        		'filter'=>$filter, 
        		'text'=>$text,
                'attendanceTable'=>$attendanceTable,
                'studentSessionTable'=> new StudentSessionTable()

        ));
        
         
         
    }


    public function activeAction()
    {
        // TODO Auto-generated ParentsController::indexAction() default action

        $table = new StudentSessionTable();

        $attendanceTable = new AttendanceTable($this->getServiceLocator());

        $paginator = $table->getActiveStudents();

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Active Students').': '.$table->getTotalActiveStudents(),
            'attendanceTable'=>$attendanceTable,
            'studentSessionTable'=>$table
        ));



    }


    public function addAction()
    {
    	$output = array();
    	$parentsTable = new StudentTable($this->getServiceLocator());
    	$form = new StudentForm(null,$this->getServiceLocator());
    	$filter = new StudentFilter($this->getServiceLocator());
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldTable = new StudentFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getAllFields();



    	if ($this->request->isPost()) {
    
    		$form->setInputFilter($filter);
    		$data = $this->request->getPost();


    	//	$form->setData($data);
            $form->setData(array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            ));
    		if ($form->isValid()  && !$parentsTable->emailExists($data['email']) ) {
    			 
    			$data = $form->getData();



                $array = [
                    'first_name'=>$data['first_name'],
                    'last_name'=>$data['last_name'],
                    'mobile_number'=>$data['mobile_number'],
                    'email'=>$data['email'],
                    'status'=>$data['status'],
                    'student_created'=>time()
                ];




    			$array[$parentsTable->getPrimary()]=0;
    		//	$array['password'] = md5('password');
                $studentPassword = substr(md5($data['email'].time().rand(0,1000000)),0,6);
                $array['password']= md5($studentPassword);
                $studentId = $parentsTable->saveRecord($array);


                //store dp
                if(!empty($data['picture']['name'])){


                    $file = $data['picture']['name'];
                    $newPath = $this->uploadDir.'/'.time().$studentId.'_'.sanitize($file);
                    $this->makeUploadDir();
                    rename($data['picture']['tmp_name'],'public/'.$newPath);

                    $parentsTable->update(['picture'=>$newPath],$studentId);

                }


                $fields= $registrationFieldsTable->getAllFields();
                foreach($fields as $row){
                    $value = $data['custom_'.$row->registration_field_id];
                    if($row->type != 'file'){

                        $studentFieldTable->saveField($studentId,$row->registration_field_id,$value);
                    }
                    elseif(!empty($value['name'])){

                        $file = $value['name'];
                        $newPath = $this->uploadDir.'/'.time().$studentId.'_'.sanitize($file);
                        $this->makeUploadDir();
                        rename($value['tmp_name'],'public/'.$newPath);
                        $studentFieldTable->saveField($studentId,$row->registration_field_id,$newPath);

                    }
                    else{
                        $studentFieldTable->saveField($studentId,$row->registration_field_id,'');
                    }

                }


    			//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
    			$output['message'] = __('Record Added!');
    			$form = new StudentForm(null,$this->getServiceLocator());

                //send email

                $title = __('New Account Details');
                $senderName = $this->getSetting('general_site_name',$this->getServiceLocator());

                $firstName = $array['first_name'];
                $recipientEmail = $array['email'];
                $siteUrl = $this->getBaseUrl();
                $message = __('new-account-mail',['firstName'=>$firstName,'studentPassword'=>$studentPassword,'senderName'=>$senderName,'siteUrl'=>$siteUrl,'recipientEmail'=>$recipientEmail]);
                $this->sendEmail($recipientEmail,$title,$message);



    		}
            elseif($parentsTable->emailExists($data['email'])){
                $output['message'] = __('save-fail-email-is-assoc',['email'=>$data['email']]);
            }
    		else{
    			$output['message'] = __('save-failed-msg');


    		}
    
    	}
    
    	$output['form'] = $form;
    	$output['pageTitle']= __('Add Student');
        $output['action'] = 'add';
        $output['id']=0;
    	return new ViewModel($output);
    
    }
    
     
    
    public function viewAction()
    {
    	$studentTable = new StudentTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $registrationFields = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldsTable = new StudentFieldTable($this->getServiceLocator());
    	$id = $this->params('id');
    	$row = $studentTable->getStudent($id);

        $attendance = $attendanceTable->getStudentRecords(false,$id);
    	
    	$viewModel = new ViewModel();
        if(empty($_GET['noterminal'])){
            $viewModel->setTerminal(true);
        }

        $customRecords = $studentFieldsTable->getStudentRecordsAll($id);

        $validator = new IsImage();

    	$viewModel->setVariables(array(
    			'row'=>$row,
                'attendance'=>$attendance,
                'custom'=>$customRecords,
                'pageTitle'=>__('Student Details').': '.$row->last_name.' '.$row->first_name,
                'validator'=>$validator,
            'attendanceTable'=>$attendanceTable
    	));
    	return $viewModel;
    }
    
    public function editAction()
    {
    	$output = array();
       	$studentsTable = new StudentTable($this->getServiceLocator());
    	$form = new StudentForm(null,$this->getServiceLocator());
        $form->setServiceLocator($this->getServiceLocator());
    	$filter = new StudentFilter($this->getServiceLocator());
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldTable = new StudentFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getAllFields();
    	$id = $this->params('id');


        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getAllFields();
    
    	$row = $studentsTable->getRecord($id);
    	if ($this->request->isPost()) {
    
    		$form->setInputFilter($filter);

            $form->setData(array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            ));

            //check if email is valid
            $validEmail = true;
            $postEmail = $this->request->getPost('email');
            if($row->email != $postEmail && $studentsTable->emailExists($postEmail)){
                $validEmail = false;
            }


    		if ($form->isValid() && $validEmail) {

                $data = $form->getData();

                $array = [
                    'first_name'=>$data['first_name'],
                    'last_name'=>$data['last_name'],
                    'mobile_number'=>$data['mobile_number'],
                    'email'=>$data['email'],
                    'status'=>$data['status'],
                ];


                //store dp
                if(!empty($data['picture']['name'])){
                    @unlink('public/'.$row->picture);

                    $file = $data['picture']['name'];
                    $newPath = $this->uploadDir.'/'.time().$id.'_'.sanitize($file);
                    $this->makeUploadDir();
                    rename($data['picture']['tmp_name'],'public/'.$newPath);
                    $array['picture'] = $newPath;

                }



         $array[$studentsTable->getPrimary()]=$id;
    			$studentsTable->saveRecord($array);

                $fields= $registrationFieldsTable->getAllFields();
                foreach($fields as $row){
                    $fieldRow = $studentFieldTable->getStudentFieldRecord($id,$row->registration_field_id);
                    $value = $data['custom_'.$row->registration_field_id];
                    if($row->type != 'file'){

                        $studentFieldTable->saveField($id,$row->registration_field_id,$value);
                    }
                    elseif(!empty($value['name'])){

                        @unlink('public/'.$fieldRow->value);

                        $file = $value['name'];
                        $newPath = $this->uploadDir.'/'.time().$id.'_'.sanitize($file);
                        $this->makeUploadDir();
                        rename($value['tmp_name'],'public/'.$newPath);
                        $studentFieldTable->saveField($id,$row->registration_field_id,$newPath);

                    }



                }
    			//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
    			$output['message'] = __('Changes Saved!');
    			 
    		}
    		else{
    			$output['message'] = __('save-failed-msg');
                if(!$validEmail){
                    $output['message'] = __('save-failed').': '.__('email-exists');
                }
    		}
    
    	}
    	else {
    		 
    		$data = UtilityFunctions::getObjectProperties($row);

            $customData = [];
            $customField = $studentFieldTable->getStudentRecords($id);
            foreach($customField as $row){
                $customData['custom_'.$row->registration_field_id]=$row->value;
            }

            $newData = array_merge($data,$customData);

    		$form->setData($newData);
    
    	}
    
    	$output['form'] = $form;
    	$output['id'] = $id;
    	$output['pageTitle']= __('Edit Student');
    	$output['row']= $studentsTable->getRecord($id);
        $output['action'] = 'edit';
        $output['id']=$id;
    
    	$viewModel = new ViewModel($output);
     //   $viewModel->setTemplate('admin/student/add.phtml');
        return $viewModel;
    }

    public function removeimageAction(){
        $id = $this->params('id');
        $studentTable = new StudentTable($this->getServiceLocator());
        $studentTable->update(['picture'=>null],$id);
        $this->flashmessenger()->addMessage(__('display-picture-removed'));
        return $this->goBack();
    }


    public function changepasswordAction(){

        $id = $this->params('id');
        $form = $this->getPasswordResetForm();
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid())
            {
                $data = $form->getData();

                $studentTable = new StudentTable($this->getServiceLocator());
                $studentTable->update(['password'=>md5($data['password'])],$id);
                $this->flashmessenger()->addMessage(__('Password changed'));
                if(!empty($data['notify'])){
                    $subject = __('password-changed-subj');
                    $message = __('password-changed-msg').$data['password'];
                    $this->notifyStudent($id,$subject,$message);
                }

            }
            else{
                $this->flashmessenger()->addMessage($this->getFormErrors($form));
            }


        }

       return $this->goBack();
    }

    private function getPasswordResetForm(){
        $form = new BaseForm();
        $form->createPassword('password','Password',true);
        $form->createPassword('confirm_password','Confirm Password',true);
        $form->createCheckbox('notify','Send new password to student?',1);
        $form->setInputFilter($this->getPasswordResetFilter());
        return $form;
    }

    private function getPasswordResetFilter(){
        $filter = new InputFilter();
        $filter->add(array(
            'name'=>'password',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));


        $filter->add(array(
            'name'=>'confirm_password',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                ),
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    )
                )
            )
        ));
        $filter->add(array(
            'name'=>'notify',
            'required'=>false,
            ));
        return $filter;

    }
    
    public function deleteAction()
    {
    	$table = new StudentTable($this->getServiceLocator());
    	$id = $this->params('id');
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }



    	$this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'index'));
    }
    
    public function sessionsAction(){
        $table = new SessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());

        $filter = $this->params()->fromQuery('filter', null);


        if (empty($filter)) {
            $filter=null;
        }

        $group = $this->params()->fromQuery('group', null);
        if (empty($group)) {
            $group=null;
        }

        $sort = $this->params()->fromQuery('sort', null);
        if (empty($sort)) {
            $sort=null;
        }

        $type = $this->params()->fromQuery('type', null);
        if (empty($type)) {
            $type=null;
        }


        $payment = $this->params()->fromQuery('payment', null);
        if (!is_numeric($payment)) {
            $payment=null;
        }

        $text = new Text('filter');
        $text->setAttribute('class','form-control');
        $text->setAttribute('placeholder',__('Search'));
        $text->setValue($filter);

        $select = new Select('group');
        $select->setAttribute('class','form-control select2');
        $select->setEmptyOption('--'.__('Select Category').'--');

        $sortSelect = new Select('sort');
        $sortSelect->setAttribute('class','form-control');
        $sortSelect->setAttribute('style','max-width:100px');
        $sortSelect->setValueOptions([
            'recent'=>__('Recently Added'),
            'asc'=>__('Alphabetical (Ascending)'),
            'desc'=>__('Alphabetical (Descending)'),
            'date'=>__('Start Date'),
            'priceAsc' =>__('Price (Lowest to Highest)'),
            'priceDesc' => __('Price (Highest to Lowest)')
        ]);
        $sortSelect->setEmptyOption('--'.__('Sort').'--');
        $sortSelect->setValue($sort);

        $typeSelect = new Select('type');
        $typeSelect->setAttribute('class','form-control');
        $typeSelect->setAttribute('style','max-width:150px');
        $typeSelect->setValueOptions([
            'c'=>__('Online Course'),
            's'=>__('Training Session'),
            'b'=>__('training-online'),
        ]);
        $typeSelect->setEmptyOption('--'.__('Type').'--');
        $typeSelect->setValue($type);


        $paymentSelect = new Select('payment');
        $paymentSelect->setAttribute('class','form-control');
        $paymentSelect->setAttribute('style','max-width:180px');
        $paymentSelect->setValueOptions([
            '1'=>__('Yes'),
            '0'=>__('No'),
        ]);
        $paymentSelect->setEmptyOption('--'.__('Payment Required').'--');
        $paymentSelect->setValue($payment);

        $groupTable = new SessionCategoryTable($this->getServiceLocator());
        $groupRowset = $groupTable->getLimitedRecords(1000);
        $options =[];

        foreach($groupRowset as $row){
            $options[$row->session_category_id] = $row->category_name;
        }
        $select->setValueOptions($options);
        $select->setValue($group);

        $paginator = $table->getPaginatedRecords(true,null,null,$filter,$group,$sort,$type,false,$payment);
        $totalRecords = $table->getTotalRecords(true,null,null,$filter,$group,$sort,$type,false,$payment);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('courses-and-sessions').'  ('.$totalRecords.')',
            'attendanceTable'=>$attendanceTable,
            'studentSessionTable'=>$studentSessionTable,
            'filter'=>$filter,
            'group'=>$group,
            'text'=>$text,
            'select'=>$select,
            'sortSelect'=>$sortSelect,
            'sort'=>$sort,
            'typeSelect'=>$typeSelect,
            'type'=>$type,
            'paymentSelect'=>$paymentSelect,
            'payment'=>$payment
        ));

    }

    public function addsessionAction(){
        $table = new SessionTable($this->getServiceLocator());
        $output = array();
        $output['id']=0;

        $filter = new SessionFilter();
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $lessonGroupTable = new LessonGroupTable($this->getServiceLocator());
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $type = $this->params('param1');
        $dbType = $type;
        if(empty($dbType)){
            $dbType = 's';
        }
        elseif($dbType=='b'){
            $dbType=null;
        }
        $form = new SessionForm(null,$this->getServiceLocator(),$dbType);
        $lessons = $lessonTable->getLimitedLessonRecords($dbType,5000);
        $total = $lessons->count();
        $output['lessons'] = $lessons;

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setInputFilter($filter);
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();



                if(empty($data['session_date'])){
                    $date = time();
                }
                else{
                    $date = strtotime($data['session_date']);
                }

                if(empty($data['session_end_date'])){
                    $endDate = time();
                }
                else{
                    $endDate = strtotime($data['session_end_date']);
                }

                if(empty($data['enrollment_closes'])){
                    $closesOn = $endDate;
                }
                else{
                    $closesOn = strtotime($data['enrollment_closes']);
                }


                $dbData = array(
                    'session_name'=>$data['session_name'],
                    'session_date'=>$date,
                    'session_end_date'=>$endDate,
                    'session_status'=>$data['session_status'],
                    'payment_required'=>$data['payment_required'],
                    'amount'=>$data['amount'],
                    'enrollment_closes'=>$closesOn,
                    'venue'=>$data['venue'],
                    'description'=>$data['description'],
                    'session_type'=>$type,
                    'picture'=>$data['picture'],
                    'short_description'=>$data['short_description'],
                    'enable_forum'=>$data['enable_forum'],
                    'enable_discussion'=>$data['enable_discussion'],
                );



               $sessionId= $table->addRecord($dbData);
                if(is_array($formData['session_instructor_id'])){
                    foreach($formData['session_instructor_id'] as $value){
                        $groupId = $value[0];
                        $sessionInstructorTable->addRecord([
                            'session_id'=>$sessionId,
                            'account_id'=>$groupId
                        ]);
                    }
                }


               // $rowset = $lessonTable->getRecords();
                foreach($lessons as $row){
                    if(!empty($data['lesson_'.$row->lesson_id])){

                        $ldata = array(
                            'lesson_id'=>$row->lesson_id,
                            'session_id'=>$sessionId,
                            'lesson_venue'=>$data['lesson_venue_'.$row->lesson_id],
                            'lesson_start'=>$data['lesson_start_'.$row->lesson_id],
                            'lesson_end'=>$data['lesson_end_'.$row->lesson_id],
                            'sort_order'=>$data['sort_order_'.$row->lesson_id]
                        );

                        if(!empty($data['lesson_date_'.$row->lesson_id])){
                            $ldata['lesson_date'] = strtotime($data['lesson_date_'.$row->lesson_id]);
                        }

                        $sessionLessonTable->addRecord($ldata);

                    }


                }
                //arrange lessons
                $sessionLessonTable->arrangeSortOrdersDateSorted($sessionId);
           /*     $this->flashMessenger()->addMessage('Session added');
                $this->redirect()->toRoute('admin/default',['controller'=>'student','action'=>'sessions']);*/


                $this->flashMessenger()->addMessage(__('session-added-post-classes'));
                $this->redirect()->toRoute('admin/default',['controller'=>'session','action'=>'sessionclasses','id'=>$sessionId]);

            }
            else{
                $output['message'] = $this->getFormErrors($form);
                if(isset($formData['session_instructor_id'])){
                    foreach($formData['session_instructor_id'] as $value){
                        $groupId = $value[0];
                        $formData['session_instructor_id[]'][] = $groupId;
                    }
                }


                $form->setData($formData);

                if ($formData['picture']) {
                    $output['display_image']= resizeImage($formData['picture'], 100, 100,$this->getBaseUrl());
                }
                else{
                    $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
                    $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
                }
            }
        }
        else{
            $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
            $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }
        $output['form'] = $form;
        $output['action'] = 'add';
        $output['type'] = $type;
        $output['pageTitle'] = ($type=='s')? __('add-session-title'):__('add-session-online-title');
        $output['lessonGroupTable'] = new LessonToLessonGroupTable($this->getServiceLocator());
        return $output;
    }

    public function editsessionAction(){
        $table = new SessionTable($this->getServiceLocator());
        $output = array();
        $output['pageTitle'] = __('Edit Session');
        $output['lessonGroupTable'] = new LessonToLessonGroupTable($this->getServiceLocator());
        $id = $this->params('id');
        $sessionRow = $table->getRecord($id);
        $type = $sessionRow->session_type;
        $dbType = 's';
        if($sessionRow->session_type=='b'){
            $dbType=null;
        }

        $form = new SessionForm(null,$this->getServiceLocator(),$dbType);



        $filter = new SessionFilter();
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $lessons = $lessonTable->getLimitedLessonRecords($dbType,5000);

        $total = $lessons->count();
        $output['lessons'] = $lessons;
        if(empty($total)){
            $link = $this->url()->fromRoute('admin/default',array('controller'=>'lesson','action'=>'add'));
            $message = __('no-classes-msg',['link'=>$link]);
            $output['message']= $message;

        }

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setInputFilter($filter);
            $form->setData($formData);

            if($form->isValid()){

                $data = $form->getData();
                if(empty($data['session_date'])){
                    $date = time();
                }
                else{
                    $date = strtotime($data['session_date']);
                }

                if(empty($data['session_end_date'])){
                    $endDate = time();
                }
                else{
                    $endDate = strtotime($data['session_end_date']);
                }

                if(empty($data['enrollment_closes'])){
                    $closesOn = $endDate;
                }
                else{
                    $closesOn = strtotime($data['enrollment_closes']);
                }


                $dbData = array(
                    'session_name'=>$data['session_name'],
                    'session_date'=>$date,
                    'session_end_date'=>$endDate,
                    'session_status'=>$data['session_status'],
                    'payment_required'=>$data['payment_required'],
                    'amount'=>$data['amount'],
                    'enrollment_closes'=>$closesOn,
                    'venue'=>$data['venue'],
                    'description'=>$data['description'],
                    'picture'=>$data['picture'],
                    'short_description'=>$data['short_description'],
                    'enable_discussion'=>$data['enable_discussion'],
                    'enable_forum'=>$data['enable_forum'],
                );


                $table->update($dbData,$id);

                $sessionInstructorTable->clearSessionRecords($id);
                if(!empty($formData['session_instructor_id'])){
                    foreach($formData['session_instructor_id'] as $value){
                        $groupId = $value[0];
                        $sessionInstructorTable->addRecord([
                            'session_id'=>$id,
                            'account_id'=>$groupId
                        ]);
                    }
                }




                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'sessions'));
            }
            else{

                $output['message'] = $this->getFormErrors($form);
                foreach($formData['session_instructor_id'] as $value){
                    $groupId = $value[0];
                    $formData['session_instructor_id[]'][] = $groupId;
                }
                $form->setData($formData);


            }
        }
        else{
            $row = $table->getRecord($id);
            $data = UtilityFunctions::getObjectProperties($row);
            $data['session_date'] = date('Y-m-d',$row->session_date);
            $data['session_end_date'] = date('Y-m-d',$row->session_end_date);
            $data['enrollment_closes'] = date('Y-m-d',$row->enrollment_closes);


            $rowset = $sessionInstructorTable->getSessionRecords($id);
            foreach($rowset as $groupRow){
                $data['session_instructor_id[]'][] = $groupRow->account_id;
            }


            //get session lessons
            $rowset = $sessionLessonTable->getSessionRecords($id);
            foreach($rowset as $row){
                $data['lesson_'.$row->lesson_id]=$row->lesson_id;
                if(!empty($row->lesson_date)){
                    $data['lesson_date_'.$row->lesson_id]= date('Y-m-d',$row->lesson_date);
                }

                $data['lesson_venue_'.$row->lesson_id] = $row->lesson_venue;
                $data['lesson_start_'.$row->lesson_id] = $row->lesson_start;
                $data['lesson_end_'.$row->lesson_id] = $row->lesson_end;
                if(!empty($row->sort_order)){
                    $data['sort_order_'.$row->lesson_id]= $row->sort_order;
                }
            }

            $form->setData($data);
        }

        $row = $table->getRecord($id);
        if ($row->picture && file_exists(DIR_MER_IMAGE . $row->picture) && is_file(DIR_MER_IMAGE . $row->picture)) {
            $output['display_image'] = resizeImage($row->picture, 100, 100,$this->getBaseUrl());
        } else {

            $output['display_image'] = resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }


        $output['no_image']= $this->getBaseUrl().'/'.resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());

        $output['form'] = $form;
        $output['action'] = 'edit';
        $output['id'] = $id;
        $output['type'] = $type;
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/student/addsession.phtml');
        return $viewModel;
    }


    public function duplicatesessionAction(){

        $id = $this->params('id');

        //get tables
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $sessionLessonAccountTable = new SessionLessonAccountTable($this->getServiceLocator());
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $sessionToSessionCategoryTable = new SessionToSessionCategoryTable($this->getServiceLocator());

        //now get session records
        $sessionRow = $sessionTable->getRecord($id);
        $sessionLessonRowset = $sessionLessonTable->getSessionRecords($id);
        $sessionLessonAccountRowset = $sessionLessonAccountTable->getSessionRecords($id);
        $sessionInstructorRowset = $sessionInstructorTable->getSessionRecords($id);
        $sessionToSessionCategoryRowset = $sessionToSessionCategoryTable->getSessionRecords($id);

        //create row
        $sessionArray= UtilityFunctions::getObjectProperties($sessionRow);
        unset($sessionArray['session_id']);
        $newId = $sessionTable->addRecord($sessionArray);

        //now get lessons
        foreach($sessionLessonRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);
            unset($data['lesson_name'],$data['session_lesson_id'],$data['lesson_type'],$data['picture'],$data['content'],$data['test_required'],$data['test_id']);
            $data['session_id']=$newId;
            $sessionLessonTable->addRecord($data);
        }

        //get instructors
        foreach($sessionLessonAccountRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);
            unset($data['session_lesson_account_id']);
            $data['session_id']= $newId;
            $sessionLessonAccountTable->addRecord($data);
        }

        foreach($sessionInstructorRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);
            unset($data['session_instructor_id']);
            $data['session_id']= $newId;
            $newData = [
              'session_id'=>$data['session_id'],
                'account_id'=>$data['account_id']
            ];
            if(!empty($data['account_id'])){
                $sessionInstructorTable->addRecord($newData);
            }


        }

        foreach($sessionToSessionCategoryRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);
            unset($data['session_to_session_category_id'],$data['category_name']);
            $data['session_id']= $newId;
            $sessionToSessionCategoryTable->addRecord($data);
        }

        $this->flashMessenger()->addMessage(__('record-duplicated'));
        $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'sessions'));


    }


    private function lessonSelected($data){
        $lessonTable = new LessonTable($this->getServiceLocator());
        $rowset = $lessonTable->getRecords();
        $valid=false;
        foreach($rowset as $row){
            if(!empty($data['lesson_'.$row->lesson_id])){
                $valid = true;
            }
        }

        return $valid;
        }

    public function createclassAction()
    {
        $output = array();
        $lessonTable = new LessonTable($this->getServiceLocator());

        $form = new LessonForm(null,$this->getServiceLocator());
        $filter = new LessonFilter();
        $output['type'] = $this->request->getQuery('type');

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                $array[$lessonTable->getPrimary()]=0;
                unset($array['lesson_group_id[]']);
                $id = $lessonTable->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                $output['message'] = __('Record Added!');
                $form = new LessonForm(null,$this->getServiceLocator());
                $output['lesson_id'] = $id;

                $output['row']= $lessonTable->getRecord($id);

                $sessionForm = new SessionForm(null,$this->getServiceLocator());
                $output['form'] = $sessionForm;
                $viewModel = new ViewModel($output);
                $viewModel->setTerminal(true);
                return $viewModel;
            }
            else{
                $output['message'] = __('save-failed-msg');
                $messages=$form->getMessages();
                print_r($messages);
                print_r($output);
                exit();

            }

        }
        else{
            exit('Invalid request');
        }



    }


    public function deletesessionAction(){
        $table = new SessionTable($this->getServiceLocator());
        try{
            $id = $this->params('id');
            $table->deleteRecord($id);
            $this->flashMessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }


        $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'sessions'));

    }

    public function attendanceAction()
    {
        $output = array();
        $sessionTable = new SessionTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());

        $lessonId = new Select('lesson_id');
        $lessonId->setAttribute('class','form-control select2');
        $lessonId->setEmptyOption('--'.__('Select a Class').'--');
        $lessonId->setAttribute('required','required');
        $lessonId->setAttribute('data-ng-model','lesson_id');
        $lessonId->setAttribute('data-ng-options','o.id as o.name for o in lessonList');


        $sessionId = new Select('session_id');
        $sessionId->setAttribute('class','form-control select2');
        $sessionId->setEmptyOption('--'.__('Select a Session/Course').'--');
        $sessionId->setAttribute('required','required');
        $sessionId->setAttribute('data-ng-model','session_id');
        $sessionId->setAttribute('data-ng-change','loadLessons()');



        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(500);

        $options = array();
        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }

        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $rowset = $sessionInstructorTable->getAccountRecords(ADMIN_ID);
        foreach($rowset as $row){
            $options[$row->session_id] = $row->session_name;
        }

        $sessionId->setValueOptions($options);

        $rowset = $lessonTable->getRecords();
        $options = array();
        foreach($rowset as $row){
            $options[$row->lesson_id]=$row->lesson_name;
        }

      //  $lessonId->setValueOptions($options);



        $output['lesson_id'] = $lessonId;
        $output['session_id'] = $sessionId;
        $output['pageTitle']=__('Attendance');
        return $output;

    }

    public function attendancebulkAction(){

        $output = array();
        $sessionTable = new SessionTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());

        $lessonId = new Select('lesson_id');
        $lessonId->setAttribute('class','form-control select2');
        $lessonId->setEmptyOption('--'.__('Select a Class').'--');
        $lessonId->setAttribute('required','required');
        $lessonId->setAttribute('data-ng-model','lesson_id');
        $lessonId->setAttribute('data-ng-options','o.id as o.name for o in lessonList');

        $sessionId = new Select('session_id');
        $sessionId->setAttribute('class','form-control select2');
        $sessionId->setEmptyOption('--'.__('Select a Session/Course').'--');
        $sessionId->setAttribute('required','required');
        $sessionId->setAttribute('data-ng-model','session_id');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $basePathHelper = $viewHelperManager->get('basePath');
        $basepath = $basePathHelper();
        $sessionId->setAttribute('data-ng-change',"loadBulkStudents()");



        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(500);

        $options = array();
        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $rowset = $sessionInstructorTable->getAccountRecords(ADMIN_ID);
        foreach($rowset as $row){
            $options[$row->session_id] = $row->session_name;
        }
        $sessionId->setValueOptions($options);

        $rowset = $lessonTable->getRecords();
        $options = array();
        foreach($rowset as $row){
            $options[$row->lesson_id]=$row->lesson_name;
        }

       // $lessonId->setValueOptions($options);



        $output['lesson_id'] = $lessonId;
        $output['session_id'] = $sessionId;
        $output['pageTitle']=__('attendance-bulk');
        return $output;

    }

    public function getstudentsAction()
    {
        $studentTable = new StudentTable($this->getServiceLocator());
        $filter = $this->params()->fromQuery('filter', null);
        $data = array();

        if(!empty($filter)){
            $rowset = $studentTable->getStudents(true,$filter);
            $rowset->setCurrentPageNumber(1);
            $rowset->setItemCountPerPage(100);


            foreach($rowset as $row){
                $data[]=array(
                    'student_id'=>$row->student_id,
                    'first_name'=>$row->first_name,
                    'last_name'=>$row->last_name,
                    'email'=>$row->email
                );
            }
        }

        exit(json_encode($data)); 

    }

    public function getsessionstudentsAction()
    {
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $session = $this->params('id');
        $data = array();
        if(!empty($session))
        {
            $rowset = $studentSessionTable->getSessionRecords(false,$session,true);
            foreach($rowset as $row){
                $data[]=array(
                    'student_id'=>$row->student_id,
                    'first_name'=>$row->first_name,
                    'last_name'=>$row->last_name,
                    'email'=>$row->email
                );
            }
        }

        exit(json_encode($data)); 
    }

    public function processattendanceAction(){
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $sessionLessonTable= new SessionLessonTable($this->getServiceLocator());
        $data = $this->request->getPost();
        $data = $_POST;
        $data = file_get_contents("php://input");
        /*
        print_r($data);
        $json = '';
        foreach($data as $key=>$value){
            $json = json_decode($key);
        }
        */
        $json = json_decode($data);
        $json = (array) $json;
        $json['students'] = (array) $json['students'];


        $lessonId = $json['lesson_id'];
        $sessionId = $json['session_id'];
        foreach($json['students'] as $value){
            $data = array(
                'lesson_id'=>$lessonId,
                'session_id'=>$sessionId,
                'student_id'=>$value->student_id,
                'attendance_date'=>$sessionLessonTable->getLessonDate($sessionId,$lessonId)
            );
        //    print_r($data);
            $attendanceTable->setAttendance($data);
        }

        echo json_encode(array('status'=>true));

        exit();
    }

    public function sessionattendeesAction(){
        $sessionTable = new SessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $id = $this->params('id');
        $rowset = $attendanceTable->getGroupedSessionRecords(false,$id);
        $output = array(
            'rowset'=>$rowset,
            'attendanceTable'=>$attendanceTable
        );
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function sessionenrolleesAction(){
        $sessionTable = new SessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $id = $this->params('id');
        $rowset = $studentSessionTable->getSessionRecords(false,$id,true);
        $output = array(
            'rowset'=>$rowset,
            'attendanceTable'=>$attendanceTable
        );
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/student/sessionattendees.phtml');
        $viewModel->setTerminal(true);
        return $viewModel;
    }


    public function enrollAction()
    {
        $id = $this->params('id');
        $sessionTable = new SessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $studentTable = new StudentTable($this->getServiceLocator());
        $studentRow = $studentTable->getRecord($id);

        $select = new Select('session_id');
        $select->setAttribute('class','form-control');
        $select->setAttribute('required','required');


        $options = array();
        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(300);

        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }
        $select->setValueOptions($options);
        $select->setEmptyOption('--'.__('Select a Session/Course').'--');
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $sessionId = $post['session_id'];

            if(!$this->canEnrollToSession($sessionId)){
                $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'index'));
            }

            // $studentSessionTable->addRecord(array('student_id'=>$id,'session_id'=>$sessionId));
            $this->enrollStudent($id,$sessionId);
            $this->flashMessenger()->addMessage(__('you-have-enrolled').' '.$studentRow->first_name.' '.$studentRow->last_name);
            $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'index'));
        }


        $output = array(
            'select' =>$select,
            'id'=>$id,
            'student'=>$studentRow
        );
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;
    }




    public function exportAction(){

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $registrationFieldTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldsTable = new StudentFieldTable($this->getServiceLocator());

        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die("Unable to open file!");
        $id = $this->params('id');
        $sessionRow = $sessionTable->getRecord($id);
        $totalRecords = $studentSessionTable->getTotalForSession($id);
        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);
        $columns = array(__('ID'),__('Last Name'),__('First Name'),__('Telephone'),__('Email'));

        $fields = $registrationFieldTable->getAllFields();
        foreach($fields as $row){
            $columns[] = $row->name;
        }



        fputcsv($myfile,$columns );
        for($i=1;$i<=$totalPages;$i++){
            $paginator = $studentSessionTable->getSessionRecords(true,$id,true);
            $paginator->setCurrentPageNumber($i);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row){
                $csvData = array($row->student_id,$row->last_name,$row->first_name,$row->mobile_number,$row->email);

                $fields = $registrationFieldTable->getAllFields();
                foreach($fields as $field){
                    $fieldRow = $studentFieldsTable->getStudentFieldRecord($row->student_id,$field->registration_field_id);
                    if(empty($fieldRow)){
                        $csvData[] ='';
                    }
                    elseif($fieldRow->type=='checkbox'){
                        $csvData[] = boolToString($fieldRow->value);
                    }
                    else{
                        $csvData[] = $fieldRow->value ;
                    }


                }



                fputcsv($myfile,$csvData );

            }



        }
        $paginator = array();
        fclose($myfile);
        header('Content-type: text/csv');
        $sessionName = $sessionRow->session_name;
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.$sessionName.'_student_export_'.date('d/M/Y').'.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();
    }


    public function exportbulkattendanceAction(){

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $registrationFieldTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldsTable = new StudentFieldTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());

        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die("Unable to open file!");
        $id = $this->params('id');
        $sessionRow = $sessionTable->getRecord($id);
        $totalRecords = $studentSessionTable->getTotalForSession($id);
        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);
        $columns = array(__('ID'),__('First Name'),__('Last Name'),__('Telephone'),__('Email'));

        //get lessons
        $lessons = $sessionLessonTable->getSessionRecords($id);
        $emptyArray= [];
        foreach($lessons as $row){
            $columns[] = $row->lesson_id.'_'.limitLength($row->lesson_name,50);
            $emptyArray[] = '';
        }



        fputcsv($myfile,$columns );
        for($i=1;$i<=$totalPages;$i++){
            $paginator = $studentSessionTable->getSessionRecords(true,$id,true);
            $paginator->setCurrentPageNumber($i);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row){
                $csvData = array($row->student_id,$row->first_name,$row->last_name,$row->mobile_number,$row->email);
                $csvData = array_merge($csvData,$emptyArray);


                fputcsv($myfile,$csvData );

            }



        }
        $paginator = array();
        fclose($myfile);
        header('Content-type: text/csv');
        $sessionName = $sessionRow->session_name;
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.$sessionName.'_attendance_student_export_'.date('d/M/Y').'.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();
    }


    public function attendanceimportAction(){
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $sessionLessonTable= new SessionLessonTable($this->getServiceLocator());

        $output = array();
        $sessionTable = new SessionTable($this->getServiceLocator());

        $sessionId = new Select('session_id');
        $sessionId->setAttribute('class','form-control select2');
        $sessionId->setEmptyOption('--'.__('Select a Session/Course').'--');
        $sessionId->setAttribute('required','required');
        $sessionId->setAttribute('data-ng-model','session_id');
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $basePathHelper = $viewHelperManager->get('basePath');
        $basepath = $basePathHelper();
     //   $sessionId->setAttribute('data-ng-change',"setLink('$basepath')");



        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(500);

        $options = array();
        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $rowset = $sessionInstructorTable->getAccountRecords(ADMIN_ID);
        foreach($rowset as $row){
            $options[$row->session_id] = $row->session_name;
        }
        $sessionId->setValueOptions($options);

        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $data = $_FILES['file'];
            $sId = $post['session_id'];
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
            $total = 0;
            $failure = 0;

            $lessons = $sessionLessonTable->getSessionRecords($sId);
            $columns = array();
            foreach($lessons as $row){
                $columns[$row->lesson_id]= $row->lesson_id.'_'.limitLength($row->lesson_name,50);
            }

            //loop rows
            foreach($all_rows as $value){
                $dbData = array();
                $studentId=$value['ID'];

                foreach($columns as $key=>$value2)
                {

                    $lesson = trim(strtolower($value[$value2]));
                    if($lesson=='p'){
                        $data = array(
                            'student_id'=>$studentId,
                            'session_id'=>$sId,
                            'lesson_id'=>$key,
                            'attendance_date'=>$sessionLessonTable->getLessonDate($sId,$key)
                        );
                        $attendanceTable->setAttendance($data);
                    }
                }


                $total++;
            }
            $output['message'] = __("attendance-set-msg",['total'=>$total]);
            if(!empty($failure)){
                $output['message'] .= " $failure ".__("records failed");
            }

        }



        $output['session_id'] = $sessionId;
        $output['pageTitle']=__('attendance-import');
        return $output;
    }


    public function exporttelAction(){

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die("Unable to open file!");
        $id = $this->params('id');
        $sessionRow = $sessionTable->getRecord($id);
        $totalRecords = $studentSessionTable->getTotalForSession($id);
        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);
      //  fputcsv($myfile, array('Last Name','First Name','Telephone','Email','Class 1 - Start ','Class 1 - End','Class 2 - Start ','Class 2 - End','Class 3 - Start ','Class 3 - End','Class 4 - Start ','Class 4 - End','Class 5 - Start ','Class 5 - End','Class 6 - Start ','Class 6 - End'));
        for($i=1;$i<=$totalPages;$i++){
            $paginator = $studentSessionTable->getSessionRecords(true,$id);
            $paginator->setCurrentPageNumber($i);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row){

                fputcsv($myfile, array($row->mobile_number));

            }



        }
        $paginator = array();
        fclose($myfile);
        header('Content-type: text/csv');
        $sessionName = $sessionRow->session_name;
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.safeUrl($sessionName).'_student_tel_export_'.date('d/M/Y').'.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();
    }





    public function massenrollAction(){
        set_time_limit(86400);
        $sessionTable = new SessionTable($this->getServiceLocator());
        $studentTable = new StudentTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldsTable = new StudentFieldTable($this->getServiceLocator());

        $select = new Select('session_id');
        $select->setAttribute('class','form-control select2');
        $select->setAttribute('required','required');


        $options = array();
        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(300);

        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }
        $select->setValueOptions($options);
        $select->setEmptyOption('--'.__('Select Session/Course').'--');
        $output = array();
        if($this->request->isPost())
        {
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
            $total = 0;
            $failure = 0;
            //loop rows
            foreach($all_rows as $value){
                $dbData = array();
                $dbData['last_name']=$value['last_name'];



                $dbData['first_name'] = $value['first_name'];

                $dbData['mobile_number']=$value['mobile_number'];
                $dbData['email']=$value['email'];

                if(empty($dbData['last_name']) || empty($dbData['email'])){
                    continue;
                }

                $dbData['status']=1;
                $studentPassword = substr(md5($dbData['email'].time().rand(0,1000000)),0,6);
                $dbData['password']=md5($studentPassword);



                $dbData['student_created']=time();

                try{
                if(!$studentTable->emailExists($dbData['email'])){

                    if(!$this->canEnrollToSession($sessionId)){
                        $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'massenroll'));

                    }


                    $total++;
                    $studentId = $studentTable->addRecord($dbData);

                    $fields = $registrationFieldsTable->getAllFields();
                    foreach($fields as $row){
                        $entry = $value[$row->registration_field_id.'_'.$row->name];
                        if($row->type=='checkbox'){
                            $entry = strtolower(trim($entry));
                            switch($entry){
                                case 'yes':
                                    $entry = 1;
                                    break;
                                case 'no':
                                    $entry = 0;
                                    break;
                                default:
                                    $entry=0;
                                    break;
                            }
                        }
                        $studentFieldsTable->saveField($studentId,$row->registration_field_id,$entry);
                    }

                    $enrollData = array('student_id'=>$studentId,'session_id'=>$sessionId);
                    $studentSessionTable->addRecord($enrollData);

                    //send email

                    $title = __('New Account Details');
                    $senderName = $this->getSetting('general_site_name',$this->getServiceLocator());

                    $firstName = $value['first_name'];
                    $recipientEmail = $value['email'];
                    $siteUrl = $this->getBaseUrl();
                    $message = __('new-account-mail',['firstName'=>$firstName,'senderName'=>$senderName,'recipientEmail'=>$recipientEmail,'studentPassword'=>$studentPassword,'siteUrl'=>$siteUrl]);

                    $this->sendEmail($recipientEmail,$title,$message);



                }else{
                    $row = $studentTable->getStudentWithEmail($dbData['email']);
                    $enrollData = array('student_id'=>$row->student_id,'session_id'=>$sessionId);
                    $studentSessionTable->addRecord($enrollData);
                }

                }
                catch(\Exception $ex){
                    $failure++;
                }

            }
            $output['message'] = __("you-enrolled-total",['total'=>$total]);
            if(!empty($failure)){
                $output['message'] .= " $failure ".__('records-failed');
            }

        }

        $output['select'] = $select;
        $output['pageTitle']=__('Bulk Enroll');
        return $output;
    }



    public function certificatelistAction(){
        set_time_limit(86400);
        $sessionTable = new SessionTable($this->getServiceLocator());
        $studentTable = new StudentTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());

        $select = new Select('session_id');
        $select->setAttribute('class','form-control');
        $select->setAttribute('required','required');


        $options = array();
        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(300);

        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }
        $select->setValueOptions($options);
        $select->setEmptyOption('--'.__('Select Session/Course').'--');
        $output = array();

        if($this->request->isPost()){
            $post = $this->request->getPost();
            $id = $post['session_id'];
            $file = "public/export.txt";
            if (file_exists($file)) {
                unlink($file);
            }

            $myfile = fopen($file, "w") or die("Unable to open file!");

            $sessionRow = $sessionTable->getRecord($id);
            $totalRecords = $studentSessionTable->getTotalForSession($id);
            $rowsPerPage = 3000;
            $totalPages = ceil($totalRecords/$rowsPerPage);

            $columns = array('Last Name','First Name','Telephone','Email');
            $lessons = $sessionLessonTable->getSessionRecords($id);
            foreach($lessons as $lesson){
                $columns[] = $lesson->lesson_id.'_'.limitLength($lesson->lesson_name,30);
            }

            fputcsv($myfile,$columns);
            for($i=1;$i<=$totalPages;$i++){
                $paginator = $studentSessionTable->getSessionRecords(true,$id,true);
                $paginator->setCurrentPageNumber($i);
                $paginator->setItemCountPerPage($rowsPerPage);

                foreach ($paginator as $row){

                    $type = $post['type'];
                    $search = $post['search'];

                    if($type=='minimum')
                    {
                        if($search=='present'){
                            $totalLessons = $attendanceTable->getTotalDistinctForStudentInSession($row->student_id,$id);
                        }
                        else{
                            $totalLessons = $attendanceTable->getTotalDistinctForStudent($row->student_id);

                        }


                        if($post['quantity'] >= 0 && $totalLessons >= $post['quantity']){

                            $values = array(ucfirst(strtolower($row->last_name)),ucfirst(strtolower($row->first_name)),cleanTel($row->mobile_number),strtolower($row->email));

                            foreach($lessons as $lesson)
                            {
                                $values[] = $attendanceTable->getStudentLessonDate($row->student_id,$lesson->lesson_id);
                            }

                            fputcsv($myfile,$values);

                        }
                        elseif($post['quantity'] < 0 && $totalLessons < abs($post['quantity'])){
                            $values = array(ucfirst(strtolower($row->last_name)),ucfirst(strtolower($row->first_name)),cleanTel($row->mobile_number),strtolower($row->email));

                            foreach($lessons as $lesson)
                            {
                                $values[] = $attendanceTable->getStudentLessonDate($row->student_id,$lesson->lesson_id);
                            }

                            fputcsv($myfile,$values);
                        }

                    }
                    else{
                        $mClasses = [];
                        foreach($post as $key=>$value){


                            if(preg_match('#lesson_#',$key)){

                                if(!empty($key)){
                                    $mClasses[] = $value;
                                }

                            }

                        }

                        if($search=='present'){
                            $status = $attendanceTable->hasClassesInSession($row->student_id,$id,$mClasses);
                        }
                        else{
                            $status = $attendanceTable->hasClasses($row->student_id,$mClasses);

                        }


                        if($status){

                            $values = array(ucfirst(strtolower($row->last_name)),ucfirst(strtolower($row->first_name)),cleanTel($row->mobile_number),strtolower($row->email));

                            foreach($lessons as $lesson)
                            {
                                $values[] = $attendanceTable->getStudentLessonDate($row->student_id,$lesson->lesson_id);
                            }

                            fputcsv($myfile,$values);

                        }



                    }




                }



            }
            $paginator = array();
            fclose($myfile);
            header('Content-type: text/csv');
            $sessionName = $sessionRow->session_name;
            // It will be called downloaded.pdf
            header('Content-Disposition: attachment; filename="'.$sessionName.'_certificate_list_'.date('d/M/Y').'.csv"');

            // The PDF source is in original.pdf
            readfile($file);
            unlink($file);
            exit();



        }

        $output['select'] = $select;
        $output['pageTitle']=__('Certificate List');
        return $output;
    }



    /*****************************BEGIN IMPORT CODE********************/

    public function importsessionAction()
    {
        $sesionTable = new SessionTable($this->getServiceLocator());
        $oldSessionTable = new OldSessionTable($this->getServiceLocator());

        $rowset = $oldSessionTable->getRecords();
        foreach($rowset as $row){
            echo $row->name.'<br/>';
            $data = array(
                'session_id'=>$row->id,
                'session_name'=>$row->name,
                'session_date'=> mktime(null,null,null,null,null,$row->year),
                'session_status'=>0
            );
            $sesionTable->addRecord($data);
        }

        exit('done');
    }


/*****************************END IMPORT CODE********************/
    function getBoolean($val){
        $val = strtolower(trim($val));
        if($val='yes'){
            return 1;
        }
        elseif($val='no'){
            return 0;
        }
        else{
            return 1;
        }
    }

    function getGender($string){
        $string = strtolower($string);
        $gender = substr($string,0,1);
        return $gender;
    }

    function getAgeRange($age){
        $age = trim($age);
        $age = substr($age,0,2);
        switch($age){
            case '20':
                $id = 1;
                break;
            case '31':
                $id = 2;
                break;
            case '41':
                $id = 3;
                break;
            case '51':
                $id = 4;
                break;
            case '60':
                $id = 5;
                break;
            default:
                $id=1;
                break;
        }
        return $id;

    }

    public function getMaritalStatus($status){
        $status = trim($status);
        $table = new MaritalStatusTable($this->getServiceLocator());
        $rowset = $table->tableGateway->select(array('marital_status'=>$status));
        $total = $rowset->count();
        if(!empty($total)){
            $row = $rowset->current();
            $id = $row->marital_status_id;
        }
        else{
            $id = 1;
        }
        return $id;
    }

    public function deleteattendanceAction(){
        try{


        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $id = $this->params('id');
        $total =  $attendanceTable->deleteRecord($id);
        if(!empty($total)){
            exit(__('Record deleted'));
        }
        else{
            exit(__('Record not deleted'));
        }

        }
        catch(\Exception $ex)
        {

        }
    }

    public function exportattendanceAction(){
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $sessionTable->getRecord($id);

        $output = array();
        $students = $studentSessionTable->getSessionRecords(false,$id,true);
        //get lessons for session
        $output['lessons'] = $sessionLessonTable->getSessionRecords($id);
        $output['students'] = $students;
        $output['attendanceTable'] = $attendanceTable;
        $output['row'] = $row;
        $output['pageTitle'] = $row->session_name;
        $output['sid']=$id;

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariables($output);

        return $viewModel;
    }

    public function attendancedateAction(){

        $sessionTable = new SessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());

        $select = new Select('session_id');
        $select->setAttribute('class','form-control select2');
        $select->setAttribute('required','required');


        $options = array();
        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(300);

        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }
        $select->setValueOptions($options);
        $select->setEmptyOption('--'.__('Select Session/Course').'--');

        $lessons = $lessonTable->getRecords();

        if($this->request->isPost()){
            $post = $this->request->getPost();
            $sessionId = $post['session_id'];
            $count = 0;
            $rowset = $sessionLessonTable->getSessionRecords($sessionId);
            foreach($rowset as $row){
                if(!empty($post['lesson_'.$row->lesson_id])){
                    $count++;
                    $lessonDate = strtotime($post['lesson_'.$row->lesson_id]);
                    $attendanceTable->setDate($sessionId,$row->lesson_id,$lessonDate);

                }
            }
            $this->flashMessenger()->addMessage(__('date-set-msg',['count'=>$count]));

            $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'attendancedate'));

        }

        return array('select'=>$select,'lessons'=>$lessons,'pageTitle'=>__('Attendance Dates'));
    }

    public function csvsampleAction(){

        $registrationTable = new RegistrationFieldTable($this->getServiceLocator());
        $file = "public/sample.csv";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die("Unable to open file!");
        $fields= array('last_name','first_name','mobile_number','email');

        //get custom fields
        $rowset = $registrationTable->getAllFields();
        foreach($rowset as $row){
            $fields[] = $row->registration_field_id.'_'.$row->name;
        }

        fputcsv($myfile, $fields);


        fclose($myfile);
        header('Content-type: text/csv');
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="sample.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();

    }

    public function getsessionlessonsAction()
    {
        $sessionLessonsTable = new SessionLessonTable($this->getServiceLocator());
        $id = $this->params('id');
        $rowset = $sessionLessonsTable->getSessionRecords($id);

        $output = array();

        $lessonId = new Select('lesson_id');
        $lessonId->setAttribute('class','form-control');
        $lessonId->setEmptyOption('--'.__('Select a Class').'--');
        $lessonId->setAttribute('required','required');
        $lessonId->setAttribute('data-ng-model','lesson_id');

        $options = array();
        foreach($rowset as $row){
            $options[]= ['id'=>$row->lesson_id,'name'=>$row->lesson_name];
        }

        exit(json_encode($options));

        $lessonId->setValueOptions($options);

        $output['lesson_id'] = $lessonId;
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;

    }


    public function sessionlessonsAction()
    {
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $id = $this->params('id');
        $rowset = $sessionLessonTable->getSessionRecords($id);

        $output= ['lessons'=>$rowset];
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;
    }




    public function certificatelessonsAction()
    {
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $id = $this->params('id');
        $rowset = $sessionLessonTable->getSessionRecords($id);

        $output= ['lessons'=>$rowset];
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function sessionstudentsAction()
    {
        // TODO Auto-generated ParentsController::indexAction() default action

        $table = new StudentSessionTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $id = $this->params('id');
        $sessionRow = $sessionTable->getRecord($id);

        $totalLessons = $sessionLessonTable->getSessionRecords($id)->count();


        $attendanceTable = new AttendanceTable($this->getServiceLocator());

        $paginator = $table->getSessionRecords(true,$id,true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>$sessionRow->session_name.' '.__('Students').': '.$table->getTotalForSession($id),
            'attendanceTable'=>$attendanceTable,
            'id'=>$id,
            'totalLessons'=>$totalLessons
        ));



    }

    public function  unenrollAction(){
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $id  = $this->params('id');
        $session = $_GET['session'];
        $studentSessionTable->unenroll($id,$session);
        $this->flashMessenger()->addMessage(__('unenroll-msg'));
        $this->redirect()->toRoute('admin/default',['controller'=>'student','action'=>'sessionstudents','id'=>$session]);


    }

    public function mailsessionAction(){
        $sessionTable = new SessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $id = $this->params('id');
        $output = [];
        $count = 0;

        if(!empty($id)){
            $session = $sessionTable->getRecord($id);
            $output['subTitle'] = __('send-mail-st1',['total'=>$studentSessionTable->getTotalForSession($id),'session'=>$session->session_name]);
            $output['pageTitle']= __('Send Message').' : '.$session->session_name;
            $output['totalStudents'] = $studentSessionTable->getTotalForSession($id);
            $output['id'] = $id;
            $output['smsTitle'] = __('send-mail-sms1',['total'=>$studentSessionTable->getTotalForSession($id),'session'=>$session->session_name]);

        }
        else{

            $output['id'] = 0;
            $output['subTitle'] = __('send-mail-st2',['total'=>$studentSessionTable->getTotalForSession($id)]);

            $output['pageTitle']= __('Send Message').' : '.__('All Students');
            $output['totalStudents'] = Student::count();
            $output['smsTitle'] = __('send-mail-sms2',['total'=>$studentSessionTable->getTotalForSession($id)]);

        }



        if($this->request->isPost())
        {

            $message = $this->request->getPost('message');
            $senderName = $this->request->getPost('name');
            $senderEmail = $this->request->getPost('senderEmail');
            $subject = $this->request->getPost('subject');
            $mode = getenv('APP_MODE');

            $studentTable = new StudentTable($this->getServiceLocator());

            if(!empty($id)){
                $totalRecords = $studentSessionTable->getTotalForSession($id);
            }
            else{
                $totalRecords = Student::count();
            }



                $rowsPerPage = 3000;
                $totalPages = ceil($totalRecords/$rowsPerPage);


                for($i=1;$i<=$totalPages;$i++){
                    if(!empty($id)){
                        $paginator = $studentSessionTable->getSessionRecords(true,$id,true);
                    }
                    else{
                        $paginator = $studentTable->getPaginatedRecords(true);
                    }

                    $paginator->setCurrentPageNumber($i);
                    $paginator->setItemCountPerPage($rowsPerPage);

                    foreach ($paginator as $row){

                        $this->sendEmail($row->email,$subject,$message,null,$senderName,$senderEmail);

                        $count++;


                    }



                }



            $this->flashMessenger()->addMessage(__('message-sent-total',['total'=>$count]));
            $this->redirect()->toUrl(selfURL());
           // $this->redirect()->toRoute('admin/default',['controller'=>'student','action'=>'sessions']);
        }

        $output['senderName'] = $this->getSetting('general_site_name',$this->getServiceLocator());
        $output['senderEmail'] = $this->getSetting('general_admin_email',$this->getServiceLocator());

        return $output;
    }



    public function transactionsAction() {
        // TODO Auto-generated TransactionController::indexAction() default action


        $this->data['pageTitle'] = __('Invoices');
        $this->data['paginator']= Invoice::orderBy('invoice_id','desc')->paginate(20);


        return new ViewModel($this->data);
    }

    public function approvetransactionAction(){
        $id = $this->params('id');
   /*     $transactionTable = new InvoiceTransactionTable($this->getServiceLocator());

        $row = $transactionTable->getRecord($id);

        $data = ['status'=>'s'];
        $transactionTable->update($data,$id);*/

        $invoice = Invoice::find($id);
        $invoice->paid = 1;
        $invoice->save();

        //add payment
        //$this->addPayment($invoice->amount,$invoice->student_id,$invoice->payment_method_id);
        $this->logPayment($id);
        //enroll student
//        $this->enrollStudent($row->student_id,$row->session_id);
        $cart = unserialize($invoice->cart);
        $cart->approve($invoice->student_id);

        //notify student
        try{
            $this->notifyStudent($invoice->student_id,'Invoice Approved!',"Your Invoice #{$id} has been approved successfully. You can now login to your account and take your course(s)");
        }
        catch(\Exception $ex){

        }

        $this->flashMessenger()->addMessage(__('invoice-approved-msg'));
        $this->redirect()->toRoute('admin/default',['controller'=>'student','action'=>'transactions']);

    }

    private  function  enrollStudent($studentId,$sessionId){
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $studentTable = new StudentTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionRow = $sessionTable->getRecord($sessionId);
        $code = generateRandomString(5);
        $studentSessionTable->addRecord(array(
            'student_id'=>$studentId,
            'session_id'=>$sessionId,
            'reg_code'=>$code,
            'enrolled_on'=>time()
        ));

        $student = $studentTable->getRecord($studentId);
        $message = "<h4>".__("Your enrollment code is")." $code</h4>";
        $emailMessage = $message.$this->getSetting('regis_email_message',$this->getServiceLocator());
        $this->sendEmail($student->email,__('Enrollment Complete'),$emailMessage,$this->getServiceLocator());
        $this->sendEnrollMessage($student,$sessionRow->session_name);
    }

    public function paymentsAction()
    {
        // TODO Auto-generated ArticlesController::indexAction() default action
        $table = new PaymentTable($this->getServiceLocator());

        $startDate=null;
        $endDate=null;
        $start=null;
        $end=null;


        $pstart=$this->params('start');
        if(!empty($pstart))
        {
            $start= $pstart;
        }
        if(isset($_GET['start'])){
            $start =$_GET['start'];
        }



        $pend=$this->params('end');
        if(!empty($pend))
        {
            $end= $pend;
        }

        if(isset($_GET['end'])){
            $end =$_GET['end'];
        }



        if(isset($start)){

            $start = str_replace('-', '/', $start);
            $startDate = strtotime($start);
        }

        if(isset($end)){
            $end = str_replace('-', '/', $end);
            $endDate = strtotime($end);
        }



        $paginator = $table->getPaymentPaginatedRecords(true,$startDate,$endDate);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        $sum = $table->getSum($startDate,$endDate);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Payments'),
            'startDate'=>str_replace('/','-',$start),
            'endDate'=>str_replace('/','-',$end),
            'sum'=>$sum
        ));

    }

    public function instructorsAction(){
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $sessionLessonAccountTable = new SessionLessonAccountTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $sessionTable->getRecord($id);

        $rowset = $sessionLessonTable->getSessionRecords($id);
        return [
          'pageTitle'=>__('Instructors for').' '.$row->session_name,
            'table'=>$sessionLessonAccountTable,
            'rowset'=>$rowset,
            'id'=>$id
        ];

    }

    public function manageinstructorsAction(){

        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $sessionLessonAccountTable = new SessionLessonAccountTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $sessionLessonTable->getRecord($id);
        //get accountlist
        $accountsTable = new AccountsTable($this->getServiceLocator());
        $rowset = $accountsTable->getRecordsSorted();

        if($this->request->isPost())
        {
            $sessionLessonAccountTable->clearSessionLessons($row->session_id,$row->lesson_id);
            $data = $this->request->getPost();
            foreach($data as $key=>$value){
                if(!empty($value)){
                    $sessionLessonAccountTable->addRecord([
                        'session_id'=>$row->session_id,
                        'lesson_id'=>$row->lesson_id,
                        'account_id'=>$value
                    ]);
                }
            }
            $this->flashMessenger()->addMessage(__('Changes Saved!'));
            $this->redirect()->toRoute('admin/default',['controller'=>'student','action'=>'instructors','id'=>$row->session_id]);

        }

        $viewModel = new ViewModel([
            'rowset'=>$rowset,
            'table'=>$sessionLessonAccountTable,
            'slrow'=>$row,
            'id'=>$id
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }


    public function importAction(){
        set_time_limit(86400);
        $sessionTable = new SessionTable($this->getServiceLocator());
        $studentTable = new StudentTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldsTable = new StudentFieldTable($this->getServiceLocator());

        $rowset = $sessionTable->getPaginatedRecords(true);
        $rowset->setCurrentPageNumber(1);
        $rowset->setItemCountPerPage(300);



        $output = array();
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $data = $_FILES['file'];
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
            $total = 0;
            $failure = 0;
            //loop rows
            foreach($all_rows as $value){
                $dbData = array();
                $dbData['last_name']=$value['last_name'];



                $dbData['first_name'] = $value['first_name'];

                $dbData['mobile_number']=$value['mobile_number'];
                $dbData['email']=$value['email'];

                if(empty($dbData['last_name']) || empty($dbData['email'])){
                    continue;
                }

                $dbData['status']=1;
                $studentPassword = substr(md5($dbData['email'].time().rand(0,1000000)),0,6);
                $dbData['password']=$studentPassword;



                $dbData['student_created']=time();

                try{
                    if(!$studentTable->emailExists($dbData['email'])){
                        $total++;
                        $studentId = $studentTable->addRecord($dbData);

                        $fields = $registrationFieldsTable->getAllFields();
                        foreach($fields as $row){
                            $entry = $value[$row->registration_field_id.'_'.$row->name];
                            if($row->type=='checkbox'){
                                $entry = strtolower(trim($entry));
                                switch($entry){
                                    case 'yes':
                                        $entry = 1;
                                        break;
                                    case 'no':
                                        $entry = 0;
                                        break;
                                    default:
                                        $entry=0;
                                        break;
                                }
                            }
                            $studentFieldsTable->saveField($studentId,$row->registration_field_id,$entry);
                        }

                        //send email

                        $title = __('New Account Details');
                        $senderName = $this->getSetting('general_site_name',$this->getServiceLocator());

                        $firstName = $value['first_name'];
                        $recipientEmail = $value['email'];

                        $siteUrl = $this->getBaseUrl();
                        $message = __('new-account-mail',['firstName'=>$firstName,'senderName'=>$senderName,'recipientEmail'=>$recipientEmail,'studentPassword'=>$studentPassword,'siteUrl'=>$siteUrl]);

                        $this->sendEmail($recipientEmail,$title,$message);



                    }

                }
                catch(\Exception $ex){
                    $failure++;
                }

            }
            $output['message'] = __("import-success",['total'=>$total]);
            if(!empty($failure)){
                $output['message'] .= " $failure ".__("records failed");
            }

        }

        $output['pageTitle']=__('Import/Export Students');
        return $output;
    }



    public function exportstudentsAction(){

        $studentSessionTable = new StudentTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $registrationFieldTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldsTable = new StudentFieldTable($this->getServiceLocator());

        $file = "public/export.txt";
        if (file_exists($file)) {
            unlink($file);
        }

        $myfile = fopen($file, "w") or die("Unable to open file!");
        $totalRecords = $studentSessionTable->getTotal();
        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);
        $columns = array(__('ID)',__('Last Name'),__('First Name'),__('Telephone'),__('Email'),__('Created On')));

        $fields = $registrationFieldTable->getAllFields();
        foreach($fields as $row){
            $columns[] = $row->name;
        }



        fputcsv($myfile,$columns );
        for($i=1;$i<=$totalPages;$i++){
            $paginator = $studentSessionTable->getPaginatedRecords(true);
            $paginator->setCurrentPageNumber($i);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row){
                $csvData = array($row->student_id,$row->last_name,$row->first_name,$row->mobile_number,$row->email,date('d/M/Y',$row->student_created));

                $fields = $registrationFieldTable->getAllFields();
                foreach($fields as $field){
                    $fieldRow = $studentFieldsTable->getStudentFieldRecord($row->student_id,$field->registration_field_id);
                    if(empty($fieldRow)){
                        $csvData[] ='';
                    }
                    elseif($fieldRow->type=='checkbox'){
                        $csvData[] = boolToString($fieldRow->value);
                    }
                    else{
                        $csvData[] = $fieldRow->value ;
                    }


                }



                fputcsv($myfile,$csvData );

            }



        }
        $paginator = array();
        fclose($myfile);
        header('Content-type: text/csv');
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="student_export_'.date('r').'.csv"');

        // The PDF source is in original.pdf
        readfile($file);
        unlink($file);
        exit();
    }

    public function deleteinvoiceAction(){
        $id = $this->params('id');
        $invoice = Invoice::find($id);
        $invoice->delete();
        $this->flashmessenger()->addMessage(__('Record deleted'));
        return $this->goBack();

    }

}