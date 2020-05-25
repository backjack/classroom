<?php

namespace Application\Controller;

use Application\Entity\Assignment;
use Application\Entity\AssignmentSubmission;
use Application\Entity\Invoice;
use Application\Entity\Student;
use Application\Entity\StudentCertificate;
use Application\Entity\Test;
use Application\Form\DiscussionFilter;
use Application\Form\DiscussionForm;
use Application\Form\StudentFilter;
use Application\Form\StudentForm;
use Application\Model\AccountsTable;
use Application\Model\AssignmentSubmissionTable;
use Application\Model\AttendanceTable;
use Application\Model\CertificateLessonTable;
use Application\Model\CertificateTable;
use Application\Model\CertificateTestTable;
use Application\Model\DiscussionAccountTable;
use Application\Model\DiscussionReplyTable;
use Application\Model\DiscussionTable;
use Application\Model\DownloadFileTable;
use Application\Model\DownloadSessionTable;
use Application\Model\DownloadTable;
use Application\Model\RegistrationFieldTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionLessonAccountTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\SessionTestTable;
use Application\Model\StudentCertificateTable;
use Application\Model\StudentFieldTable;
use Application\Model\StudentLectureTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTestTable;
use Application\Model\SurveyQuestionTable;
use Application\Model\SurveyResponseTable;
use Application\Model\SurveyTable;
use Application\Model\TestGradeTable;
use Application\Model\TestQuestionTable;
use Dompdf\Dompdf;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\Opencart\Library\Session;
use Mpdf\Mpdf;
use Zend\EventManager\EventManagerInterface;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Application\Model\StudentTable;
use Application\Model\ChildrenTable;
use Application\Model\HomeworkTable;
use Application\Model\StudentCategoriesTable;
use Application\Model\ReportsTable;
use Intermatics\UtilityFunctions;
use Zend\View\View;


/**
 * ParentsController
 *
 * @author
 *
 * @version
 *
 */
class StudentController extends AbstractController {

    use HelperTrait;

    protected $uploadDir;

    public function __construct(){
        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $this->uploadDir = 'usermedia'.$user.'/student_uploads/'.date('Y_m');



    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/student');
        }, 100);
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
	public function indexAction() {
		// TODO Auto-generated ParentsController::indexAction() default action

        $output = [];
        $viewModel = $this->forward()->dispatch('Application\Controller\Catalog',['action'=>'sessions']);
        $output['sessions'] = $viewModel->getVariables();
        $output['sessions']['paginator']->setItemCountPerPage(5);



        $viewModel = $this->forward()->dispatch('Application\Controller\Catalog',['action'=>'courses']);
        $output['courses'] = $viewModel->getVariables();
        $output['courses']['paginator']->setItemCountPerPage(5);

        $studentId = $this->getId();

        $viewModel = $this->forward()->dispatch('Application\Controller\Student',['action'=>'mysessions']);
        $output['mysessions'] = $viewModel->getVariables();
        $output['mysessions']['paginator']->setItemCountPerPage(3);


        $viewModel = $this->forward()->dispatch('Application\Controller\Student',['action'=>'notes']);
        $output['notes'] = $viewModel->getVariables();
        $output['notes']['paginator']->setItemCountPerPage(5);


        $viewModel = $this->forward()->dispatch('Application\Controller\Download',['action'=>'index']);
        $output['downloads'] = $viewModel->getVariables();
        $output['downloads']['paginator']->setItemCountPerPage(5);

        $viewModel = $this->forward()->dispatch('Application\Controller\Student',['action'=>'discussion']);
        $output['discussions'] = $viewModel->getVariables();
        $output['discussions']['paginator']->setItemCountPerPage(5);

        $viewModel = $this->forward()->dispatch('Application\Controller\Assignment',['action'=>'index']);
        $output['homework'] = $viewModel->getVariables();
        $output['homework']['paginator']->setItemCountPerPage(100);

        $totalHomework= $output['homework']['total'];
        $submissionTable = new AssignmentSubmissionTable();
        $output['homeworkPresent'] = false;
        if(!empty($totalHomework)){
            foreach ($output['homework']['paginator'] as $row){
                if(!$submissionTable->hasSubmission($this->getId(),$row->assignment_id)){
                    $output['homeworkPresent'] = true;
                }
            }

        }
        $output['controller'] = $this;
        $output['student'] = Student::find($studentId);
        $output['gradeTable'] = new TestGradeTable();

        $viewModel = $this->forward()->dispatch('Application\Controller\Student',['action'=>'certificates']);
        $output['certificate'] = $viewModel->getVariables();
        $output['certificate']['paginator']->setItemCountPerPage(7);

        //create forum topics
        $studentSessionTable = new StudentSessionTable();
        $forumTopics = $studentSessionTable->getForumTopics(true,$this->getId());
        $forumTopics->setItemCountPerPage(10);
        $output['forumTopics'] = $forumTopics;


        $this->layout('layout/student');
        $output['pageTitle'] = __('Dashboard');
        return new ViewModel ($output);
	}

	public function getStudentProgress($sessionId){
	    $attendanceTable = new AttendanceTable();

	    $session = \Application\Entity\Session::find($sessionId);
	    $totalLessons = $session->sessionLessons()->count();


	    $totalAttended  = $attendanceTable->getTotalDistinctForStudentInSession($this->getId(),$sessionId);

        if($totalLessons==0){
            $totalLessons = 1;
        }
	    //calculate percentage
        $percentage = ($totalAttended/$totalLessons) * 100;
        $percentage = round($percentage);
        return $percentage;

    }

    public function mysessionsAction(){

        if(isMobileApp()){
            return $this->redirect()->toRoute('mobile-close');
        }
        $studentId = $this->getId();
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $paginator = $studentSessionTable->getStudentRecords(true,$studentId);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        $total = $studentSessionTable->getTotalForStudent($studentId);

        $output = [
          'pageTitle'=>__('Enrolled Courses'),
            'paginator'=>$paginator,
            'id'=>$studentId,
            'studentSessionTable'=>$studentSessionTable,
            'attendanceTable'=>$attendanceTable,
            'total'=>$total
        ];
        return new ViewModel($output);
    }

    /**
     * This displays information for auto enroll
     */
    public function welcomeAction()
    {

    }

    /**
     * @return ViewModel
     * @throws \Exception
     * The student can change their account information here
     */
	public function profileAction()
	{
        $output = array();
        $studentsTable = new StudentTable($this->getServiceLocator());
        $form = new StudentForm(null,$this->getServiceLocator(),true);
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldTable = new StudentFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getActiveFields();
        $form->setServiceLocator($this->getServiceLocator());
        $filter = new StudentFilter($this->getServiceLocator(),true);
        $id = $this->getId();

        $row = $studentsTable->getRecord($id);
        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $data['status']= ($row->status=='') ? 1:$row->status;

            $form->setData(array_merge_recursive(
                    $data->toArray(),
                $this->request->getFiles()->toArray()
            ));


            if ($form->isValid()) {



                $data = $form->getData();

                //check for formatted phone number
                $formattedNo  = $this->request->getPost('fmobilenumber');
                if(!empty($formattedNo)){
                    $data['mobile_number'] = $formattedNo;
                }


                $data = removeTags($data);



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

                //update identity
                $this->getAuthService()->getStorage()->write(array(
                    'email'=>trim($array['email']),
                    'role'=>'student'
                ));

                $fields= $registrationFieldsTable->getActiveFields();
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

                $this->flashmessenger()->addMessage(__('Changes saved!'));
                $this->redirect()->toUrl(selfURL());
                $output['message'] = __('Changes saved!');

            }
            else{
                $errors = $form->getMessages();

                $fields= '';
                foreach($errors as $key=>$value){
                    $key= $form->get($key)->getLabel();

                    $fields .= '<br/><strong>'.ucfirst($key).'</strong>: ';
                    foreach($value as $msg){
                        $fields .= $msg.'. ';
                    }
                }
                $output['message'] = __('save-failed-msg').$fields;

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
        $output['pageTitle']= __('Your Profile');
        $output['row']= $studentsTable->getRecord($id);
        $output['action'] = 'edit';
        $output['table'] = new StudentFieldTable($this->getServiceLocator());



        $viewModel = new ViewModel($output);
        return $viewModel;
	}

    public function removeimageAction(){
        $id = $this->getId();
        $studentTable = new StudentTable($this->getServiceLocator());
        $studentTable->update(['picture'=>null],$id);
        $this->flashmessenger()->addMessage(__('Display picture removed'));
        return $this->goBack();
    }

    /**
     * @return ViewModel
     * This displays the list of sessions for the student to select from
     */
    public function enrollAction(){

        if(!$this->studentIsLoggedIn()){
            $this->layout(TEMPLATE);
        }

        $table = new SessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $paginator = $table->getValidSessions(true,['b','s']);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        $authService=$this->getAuthService();
        $role = UtilityFunctions::getRole();


        if($authService->hasIdentity() && $role=='student'){
            $id = $this->getId();
        }
        else{
            $id = 0;
        }

        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Enroll For A Session'),
            'studentSessionTable'=>$studentSessionTable,
            'id'=>$id,
            'terminal'=>$this->params('terminal')
        ));
    }



    public function cartAction(){

        $this->data['pageTitle'] = __('Your Orders');

        $this->data['cart'] = getCart();

        return $this->data;
    }

      public function setsessionAction(){
          $id = $this->params('id');

        if(!$this->canEnrollToSession($id)){
            $this->goBack();
        }

        $sessionTable= new SessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $session = new Container('enroll');

        $session->id = $id;
        $row = $sessionTable->getRecord($id);
        $authService = $this->getAuthService();
        $role = UtilityFunctions::getRole();
        $type = ($row->session_type=='c')? __('Course'):__('Session');
        if(!$authService->hasIdentity() || $role != 'student')
        {
            $this->redirect()->toRoute('application/signin');
        }
        elseif( (empty($row->payment_required) || $row->amount==0 ) && (empty($row->enrollment_closes) || $row->enrollment_closes > time())  && !empty($row->session_status)){


             $code = generateRandomString(5);
             $studentSessionTable->addRecord(array(
                 'student_id'=>$this->getId(),
                 'session_id'=>$id,
                 'reg_code'=>$code,
                 'enrolled_on'=>time()
             ));

             $student = $this->getStudent();

            $sessionName =$row->session_name;
             $message = __('you-suc-enroll')." $sessionName $type! <br/>"."<h4>".__('Your enrollment code is')." $code</h4>";
            // $emailMessage = $message.'<br/>'.$this->getSetting('regis_email_message',$this->getServiceLocator());
            $emailMessage = $message.$this->getSetting('regis_email_message',$this->getServiceLocator());
             $this->sendEmail($student->email,__('Enrollment Complete'),$emailMessage,$this->getServiceLocator());

             $this->sendEnrollMessage($student,$row->session_name);
            $message = __('you-suc-enroll')." $sessionName $type!";
             $this->flashMessenger()->addMessage($message);
             //$this->redirect()->toRoute('application/enroll');
            if($row->session_type!='c'){
                $this->redirect()->toRoute('session-details',array('id'=>$row->session_id));
            }
            else{
                //redirect to the course introduction page
                $this->redirect()->toRoute('application/default',['controller'=>'course','action'=>'intro','id'=>$row->session_id]);
            }

         }
        elseif(!(empty($row->payment_required) || $row->amount==0 ) && (empty($row->enrollment_closes) || $row->enrollment_closes > time()) && !empty($row->session_status))
        {
            $cart = getCart();
            $cart->addSession($id);
            return $this->redirect()->toRoute('cart');
        }
        else{
            $this->flashMessenger()->addMessage(__('enroll-not-avail').' '.$type);
            $this->goBack();
        }


    }

    public function removesessionAction(){
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());

        $id = $this->params('id');
        $session = $sessionTable->getRecord($id);
        if(empty($session->payment_required))
        {
            $studentSessionTable->tableGateway->delete(array(
                'student_id'=>$this->getId(),
                'session_id'=>$id
            ));
            $this->flashMessenger()->addMessage(__('suc-unenroll'));
        }
        else{
            $this->flashMessenger()->addMessage(__('unenroll-fail'));
        }

        return $this->goBack();
        //$this->redirect()->toRoute('application/enroll');
    }

    public function classesAction()
    {
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $id = $this->getId();

        $attendance = $attendanceTable->getStudentRecords(true,$id);

        $attendance->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $attendance->setItemCountPerPage(30);

        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'attendance'=>$attendance,
            'pageTitle'=>__('Classes Attended')
        ));
        return $viewModel;
    }

	public function passwordAction()
	{
		$output = array();
		$identity= $this->getAuthService()->getIdentity();
		$email = $identity['email'];
		$form = new BaseForm();
        $form->addCSRF();
		$accountsTable =new StudentTable($this->getServiceLocator());
		if ($this->request->isPost()) {
			$post = $this->request->getPost();
            $form->setData($post);
			if ($post['password']==$post['confirm_password'] && $form->isValid()) {
	
				$newPassword = md5(trim($post['password']));
				$accountsTable->tableGateway->update(array('password'=>$newPassword),array('email'=>$email));
				$output['message']=__('Password changed!');
					
			}
			else{
				$output['message']=__('password-not-match');
			}
		}
		$output['pageTitle']=__('Change Your Password');
        $output['form'] = $form;
		return $output;
	}

    public function notesAction(){

        $table = new StudentSessionTable($this->getServiceLocator());
        $homeworkTable = new HomeworkTable($this->getServiceLocator());
        $paginator = $table->getStudentRecords(true,$this->getId());

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('select-a-session/course'),
            'homeworkTable'=>$homeworkTable,
            'id'=>$this->getId()
        ));
    }

    private function enrolledInSession($id){
      $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        return $studentSessionTable->enrolled($this->getId(),$id);
    }
	
	public function sessionnotesAction() {
		// TODO Auto-generated ArticlesController::indexAction() default action
		$table = new HomeworkTable($this->getServiceLocator());
		$sessionTable = new SessionTable($this->getServiceLocator());
		
		$id = $this->params('id');

        if(!$this->enrolledInSession($id)){
            return $this->redirect()->toRoute('application/dashboard');
        }

		$paginator = $table->getPaginatedRecords(true,$id);
		$session = $sessionTable->getRecord($id);
	
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(30);
		return new ViewModel (array(
				'paginator'=>$paginator,
				'pageTitle'=>__('Revision Notes').': '.$session->session_name,
				'session'=>$session->session_name,
                'id'=>$id
		));
	
			
	}
	

	
	public function viewnoteAction()
	{
		$homeworktable= new HomeworkTable($this->getServiceLocator());
		$id = $this->params('id');
		$row=$homeworktable->getHomework($id);
        if(!$this->enrolledInSession($row->session_id)){
            return $this->redirect()->toRoute('application/dashboard');
        }
		return array('row'=>$row,'pageTitle'=>__('Revision Note').': '.$row->title);
	}

    public function timetableAction(){
        if(!$this->studentIsLoggedIn()){
            $this->layout(TEMPLATE);
        }

        $authService=$this->getAuthService();
        $role = UtilityFunctions::getRole();
        $id = $this->params('id');

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $studentLectureTable = new StudentLectureTable($this->getServiceLocator());
        $resumeLink = '';
        $resumeText = __('Resume');
        $enrolled = false;
        if($authService->hasIdentity() && $role=='student'){
            $studentId = $this->getId();
            if ($studentSessionTable->enrolled($studentId, $id)) {
                $enrolled= true;
                //check if student has started lecture
                if($studentLectureTable->hasLecture($studentId,$id)){
                    $lecture = $studentLectureTable->getLecture($studentId,$id);
                    if($lecture->sort_order == 1){
                        $resumeLink = $this->url()->fromRoute('view-class', ['classId' => $lecture->lesson_id, 'sessionId' => $id]);
                    }
                    else{
                        $resumeLink = $this->url()->fromRoute('view-lecture', ['lectureId' => $lecture->lecture_id, 'sessionId' => $id]);

                    }

                }
                else{

                    $resumeLink = $this->url()->fromRoute('application/default', ['controller' => 'course', 'action' => 'intro','id'=>$id]);
                    $resumeText = __('Start');
                }

            }
        }
        else{
            $studentId = 0;
        }
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $sessionLessonAccountTable = new SessionLessonAccountTable($this->getServiceLocator());

        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $discussionForm= new DiscussionForm(null,$this->getServiceLocator(),$studentId);
        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());

        $row = $sessionTable->getRecord($id);
        $rowset = $sessionLessonTable->getSessionRecords($id);



        //get instructors
        $instructors = $sessionInstructorTable->getSessionRecords($id);

        //get downloads
        $downloads = $downloadSessionTable->getSessionRecords($id);

        //get session tests
        $sessionTestTable  = new SessionTestTable($this->getServiceLocator());
        $tests = $sessionTestTable->getSessionRecords($id);

        $output= ['rowset'=>$rowset,'row'=>$row,'pageTitle'=>__('Session Details'),'table'=>$sessionLessonAccountTable,'id'=>$id,
        'studentId'=>$studentId,
        'studentSessionTable'=>$studentSessionTable,
            'instructors' => $instructors,
            'form'=>$discussionForm,
            'downloads'=>$downloads,
            'fileTable'=> new DownloadFileTable($this->getServiceLocator()),
            'resumeLink'=>$resumeLink,
            'resumeText'=>$resumeText,
            'enrolled'=>$enrolled,
            'tests'=>$tests,
            'questionTable'=>new TestQuestionTable($this->getServiceLocator()),
            'studentTest'=> new StudentTestTable($this->getServiceLocator())
        ];

        if(!$this->studentIsLoggedIn()){

            return $this->getViewModel($output);
        }
        else{
            return new ViewModel($output);
        }


    }

    public function discussionAction()
    {
        $table = new DiscussionTable($this->getServiceLocator());
        $discussionForm= new DiscussionForm(null,$this->getServiceLocator(),$this->getId());
        $discussionAccountTable = new DiscussionAccountTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $paginator = $table->getPaginatedRecordsForStudent(true,$this->getId());

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Instructor Chat'),
            'form'=>$discussionForm,
            'accountTable'=>$discussionAccountTable,
            'sessionTable'=>$sessionTable
        ));
    }

    public function adddiscussionAction()
    {

        $discussionTable = new DiscussionTable($this->getServiceLocator());
        $discussionAccountTable = new DiscussionAccountTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $form = new DiscussionForm(null,$this->getServiceLocator(),$this->getId());
        $filter = new DiscussionFilter($this->getServiceLocator());
        $form->setInputFilter($filter);

        if($this->request->isPost())
        {
            $formData = $this->request->getPost();
            $form->setData($formData);

            if($form->isValid())
            {
                $data = $form->getData();
                $data = removeTags($data);
                unset($data['account_id[]']);
                $data['created_on'] = time();
                $data['student_id'] = $this->getId();
                $discussionId = $discussionTable->addRecord($data);
                //check if there are accounts
                $title = __('New question').': '.$data['subject'];
                $user = $this->getStudent();

                //get list of sessions
                $list = '<br/><strong>'.__('en-courses-sessions').'</strong>:';
                if($studentSessionTable->getTotalForStudent($this->getId())==0){
                    $list .= __('None');
                }
                else{
                    $rowset = $studentSessionTable->getStudentRecords(false,$this->getId());
                    foreach($rowset as $row){
                        $list.=$row->session_name.', ';
                    }

                }
                $list= '<br/>';
                $message = __('new-chat-mail',['firstname'=>$user->first_name,'lastname'=>$user->last_name,'subject'=>$data['subject'],'question'=>$data['question'],'list'=>$list,'link'=>$this->getBaseUrl().'/admin']);
                $admins = 0;

                foreach($formData['account_id'] as $value){
                    $accountId = $value[0];
                    if($accountId !='admins'){

                        $this->notifyAdmin($accountId,$title,$message);
                        $discussionAccountTable->addRecord([
                            'account_id'=>$accountId,
                            'discussion_id'=> $discussionId
                        ]);
                    }
                    else{
                        $admins = 1;
                        $this->notifyAdmins($title,$message);
                    }
                }
                $discussionTable->update(['admin'=>$admins],$discussionId);


                $this->flashMessenger()->addMessage(__('your-ques-added'));
            }
            else{
                $this->flashMessenger()->addMessage($this->getFormErrors($form));
           }
        }

        $this->goBack();
        //$this->redirect()->toRoute('application/discussions');

    }

    public function addreplyAction(){
        $table = new DiscussionReplyTable($this->getServiceLocator());
        $discussionTable = new DiscussionTable($this->getServiceLocator());
        $discussionAccountTable = new DiscussionAccountTable($this->getServiceLocator());
        $form = new BaseForm();
        $form->addCSRF();
        $form->createTextArea('reply','Reply',true,null,__('Reply here'));

        $accountTable = new AccountsTable($this->getServiceLocator());
        $id = $this->params('id');
        $discussionRow = $discussionTable->getRecord($id);
        $this->validateOwner($discussionRow);
        if($this->request->isPost())
        {
            $reply = $this->request->getPost('reply');
            $form->setData($this->request->getPost());
            if(!empty($reply) && $form->isValid()){
                $data = [
                    'discussion_id'=>$id,
                    'reply'=> strip_tags($reply),
                    'replied_on'=> time(),
                    'user_id'=> $this->getId(),
                    'user_type'=>'s'
                ];
                $table->addRecord($data);
                $discussionTable->update(['replied'=>0],$id);
                $user = $this->getStudent();
                $name = $user->first_name.' '.$user->last_name;
                //send notification to admins
                $subject = __('New reply for').' "'.$discussionRow->subject.'"';
                $message = __('New reply for').' "'.$discussionRow->subject."\". $name ".__('said').": <br/>".$reply;
                $rowset = $table->getRepliedAdmins($id);
                foreach($rowset as $row){
                    try{
                        $account = $accountTable->getRecord($row->user_id);
                        if(!empty($account->email)){
                            $this->sendEmail($account->email,$subject,$message);
                        }
                    }
                    catch(\Exception $ex)
                    {

                    }

                }
                $this->flashMessenger()->addMessage(__('reply-added-msg'));
            }
            else{
                $this->flashMessenger()->addMessage(__('submission-failed-msg'));
            }

        }
        $this->redirect()->toRoute('application/viewdiscussion',['id'=>$id]);
    }

    public function viewdiscussionAction()
    {
        $table = new DiscussionReplyTable($this->getServiceLocator());
        $discussionTable = new DiscussionTable($this->getServiceLocator());
       $id = $this->params('id');
        $row= $discussionTable->getRecord($id);
        $this->validateOwner($row);
        $form = new BaseForm();
        $form->addCSRF();
        $form->createTextArea('reply','Reply',true,null,__('Reply here'));


        $paginator = $table->getPaginatedRecordsForDiscussion(true,$id);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('View Instructor Chat'),
            'row'=>$row,
            'student'=>$this->getStudent(),
            'accountTable'=> new AccountsTable($this->getServiceLocator()),
            'total'=>$table->getTotalReplies($id),
            'form'=>$form
        ));
    }


    public function certificatesAction(){
        $table = new StudentSessionTable($this->getServiceLocator());
        $clTable =new CertificateLessonTable($this->getServiceLocator());
        $ctTable = new CertificateTestTable($this->getServiceLocator());

        $id= $this->getId();

        $paginator = $table->getCertificates(true,$id);


        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>$this->setting('label_certificates',__('Certificates')),
            'clTable'=>$clTable,
            'ctTable'=>$ctTable
        ));

    }

    public function certificateAction(){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $id = $this->params('id');
        if(!$this->canAccessCertificate($id)){
            $this->flashMessenger()->addMessage(__('not-met-requirements'));
          return  $this->redirect()->toRoute('application/certificates');
        }
        $row = $certificateTable->getRecord($id);
        $html = $this->getCertificateHtml($id);

        $viewModel = new ViewModel(['html'=>$html,'row'=>$row]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    public function downloadcertificate3Action(){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $id = $this->params('id');
        if(!$this->canAccessCertificate($id)){
            $this->flashMessenger()->addMessage(__('not-met-requirements'));
            return  $this->redirect()->toRoute('application/certificates');
        }

        if(!$this->canDownload($id)){
            $this->flashMessenger()->addMessage(__('exceeded-max-downloads'));
            return  $this->redirect()->toRoute('application/certificates');
        }

        $url= $this->getBaseUrl();


        $html = $this->getCertificateHtml($id);

        require_once('vendor/tcpdf/tcpdf.php');
        // create new PDF document
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 049');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 049', PDF_HEADER_STRING);

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->SetFont('helvetica', '', 10);

        $pdf->AddPage();





        $pdf->writeHTML($html, true, 0, true, 0);

        $pdf->lastPage();
        $pdf->Output('example_049.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

    }

    public function downloadcertificate1Action(){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $id = $this->params('id');
        if(!$this->canAccessCertificate($id)){
            $this->flashMessenger()->addMessage(__('not-met-requirements'));
            return  $this->redirect()->toRoute('application/certificates');
        }

        if(!$this->canDownload($id)){
            $this->flashMessenger()->addMessage(__('exceeded-max-downloads'));
            return  $this->redirect()->toRoute('application/certificates');
        }

        $url= $this->getBaseUrl();


        $html = $this->getCertificateHtml($id);

        require_once('vendor/tcpdf/tcpdf.php');
        // create new PDF document
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
/*        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 001');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

// set default header data
/*        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

// ---------------------------------------------------------

// set default font subsetting mode
        $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

// set text shadow effect
        $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print


// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        $pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

    }

    public function downloadcertificateAction(){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $id = $this->params('id');
        if(!$this->canAccessCertificate($id)){
            $this->flashMessenger()->addMessage(__('not-met-requirements'));
          return  $this->redirect()->toRoute('application/certificates');
        }

        if(!$this->canDownload($id)){
            $this->flashMessenger()->addMessage(__('exceeded-max-downloads'));
            return  $this->redirect()->toRoute('application/certificates');
        }

        $url= $this->getBaseUrl();


        $html = $this->getCertificateHtml($id);


        $html= str_ireplace($url,'./public',$html);


        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $row= $certificateTable->getRecord($id);
        $orientation = ($row->orientation=='p')?'portrait':'landscape';
        $dompdf->setPaper('A4', $orientation);
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream(safeUrl($row->certificate_name).'.pdf');


        exit();

    }

    public function downloadcertificate2Action(){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $id = $this->params('id');
        if(!$this->canAccessCertificate($id)){
            $this->flashMessenger()->addMessage(__('not-met-requirements'));
            return  $this->redirect()->toRoute('application/certificates');
        }

        if(!$this->canDownload($id)){
            $this->flashMessenger()->addMessage(__('exceeded-max-downloads'));
            return  $this->redirect()->toRoute('application/certificates');
        }

        $url= $this->getBaseUrl();
        $row= $certificateTable->getRecord($id);
        $orientation = ($row->orientation=='p')?'P':'L';

        $html = $this->getCertificateHtml($id);


        $html= str_ireplace($url,'./public',$html);


        $style_data = "body {
        font-family: dejavusanscondensed;
}";


       $mpdf= new Mpdf(['tempDir' =>  'public/tmp']);
      //  $mpdf->SetDirectionality('rtl');

        $mpdf->WriteHTML($style_data, 1);        // The parameter 1 tells mPDF that this is CSS and not HTML

// Write the main text
    //    $mpdf->WriteHTML($html, 2);
//exit($html);
        $mpdf->WriteHTML($html);
        $mpdf->Output(safeUrl($row->certificate_name).'.pdf','D');

        exit();

    }


    public function getCertificateHtml($id){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $studentCertificateTable= new StudentCertificateTable();
        //add student record

        $sessionTable = new SessionTable($this->getServiceLocator());
        $row = $certificateTable->getRecord($id);
        $sessionRow = $sessionTable->getRecord($row->session_id);
        $student = $this->getStudent();

        $studentCertificate = $studentCertificateTable->addStudentEntry($this->getId(),$id);
        $name = $student->first_name.' '.$student->last_name;
        $elements = [
            'student_name'=>$name,
            'session_name'=>$sessionRow->session_name,
            'session_start_date'=>showDate('d/M/Y',$sessionRow->session_date),
            'session_end_date'=>showDate('d/M/Y',$sessionRow->session_end_date),
            'date_generated'=>date('d/M/Y'),
            'company_name'=> $this->getSetting('general_site_name'),
            'certificate_number' => $studentCertificate->tracking_number
        ];
        //get lessons for session
        $lessons = $sessionLessonTable->getSessionRecords($row->session_id);

        foreach($lessons as $lesson){

            if(!empty($row->any_session)){
                $date = $attendanceTable->getStudentLessonDate($this->getId(),$lesson->lesson_id);
            }
            else{
                $date = $attendanceTable->getStudentLessonDateInSession($this->getId(),$lesson->lesson_id,$row->session_id);
            }
            if(empty($date)){
                $date = 'N/A';
            }
            $elements['class_date_'.$lesson->lesson_id.'_'.strtoupper(safeUrl($lesson->lesson_name))]=$date;
        }

        $html = $row->html;



        foreach($elements as $key=>$value){
            $key = strtoupper($key);
            $html = str_replace("[$key]",$value,$html);
        }

        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';


        //
        if($_SERVER['SERVER_PORT'] != '443'){
            $html = str_ireplace('https://','http://',$html);
        }
        else{
            $html = str_ireplace('http://','https://',$html);
        }

        return $html;
    }

    public function canAccessCertificate($certificateid){
        $certificateTable = new CertificateTable($this->getServiceLocator());
        $certificateLessonTable = new CertificateLessonTable($this->getServiceLocator());
        $certificateTestTable = new CertificateTestTable($this->getServiceLocator());
        $certificateAssignmentTable = new \Application\Model\CertificateAssignmentTable();
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $studentAssignmentTable = new \Application\Model\AssignmentSubmissionTable();

        $certificateRow = $certificateTable->getRecord($certificateid);
        $studentId= $this->getId();
        //check that certificate is active
        if(empty($certificateRow->status)){
            return false;
        }

        //check that student is enrolled in session
        if(!$studentSessionTable->enrolled($this->getId(),$certificateRow->session_id)){
            $this->flashMessenger()->addMessage('You are not enrolled into the course/session required for this certificate');
           return false;
        }

        //check for attendance
        if($certificateLessonTable->getTotalForCertificate($certificateid)>0){

            $mClasses = [];
            $rowset = $certificateLessonTable->getCertificateLessons($certificateid);
            foreach($rowset as $row){
                $mClasses[] = $row->lesson_id;
            }

            if(empty($certificateRow->any_session)){
                $status = $attendanceTable->hasClassesInSession($studentId,$certificateRow->session_id,$mClasses);
            }
            else{
                $status = $attendanceTable->hasClasses($studentId,$mClasses);
            }

            if(!$status){
                $this->flashMessenger()->addMessage(__('outstanding-classes'));
                return false;
            }


        }

        if($certificateTestTable->getTotalForCertificate($certificateid)>0){
            $rowset = $certificateTestTable->getCertificateRecords($certificateid);
            foreach($rowset as $row)
            {
                $passedTest = $studentTestTable->passedTest($studentId,$row->test_id);
                if(!$passedTest){
                    $testRecord = Test::find($row->test_id);
                    $this->flashMessenger()->addMessage(__('need-take-test',['test'=>$testRecord->name]));
                    return false;
                }
            }


        }
        
        
            if($certificateAssignmentTable->getTotalForCertificate($certificateid)>0){
            $rowset = $certificateAssignmentTable->getCertificateRecords($certificateid);
            foreach($rowset as $row)
            {
                $passedAssignment = $studentAssignmentTable->passedAssignment($studentId,$row->assignment_id);
                if(!$passedAssignment){
                    $assignmentRecord = Assignment::find($row->assignment_id);
                    $this->flashMessenger()->addMessage(__('assignment-needed',['assignment'=>$assignmentRecord->title]));
                    return false;
                }
            }


        }
        

        return true;

    }

    public function canDownload($certificateid){
        $certificateTable = new CertificateTable();


        $certificateRow = $certificateTable->getRecord($certificateid);
        $studentId= $this->getId();
        $student  = Student::find($studentId);

        $totalAllowed = $certificateRow->max_downloads;
        $totalDownloaded = $student->studentCertificateDownloads->count();

        if($totalDownloaded >= $totalAllowed && $totalAllowed > 0){
            return false;
        }
        else{
            return true;
        }


    }

	public function getStudent()
	{
		$authService=$this->getAuthService();
		$identity = $authService->getIdentity();
		$email = $identity['email'];
		$parentsTable = new StudentTable($this->getServiceLocator());
		$row = $parentsTable->tableGateway->select(array('email'=>$email))->current();
		return $row;
	}



    public function getId(){
        $row = $this->getStudent();
        return $row->student_id;
    }
	
	public function getAuthService()
	{
	
		return $this->getServiceLocator()->get('StudentAuthService');
	}

    public function invoicesAction(){

        $invoices= Invoice::where('student_id',$this->getId())->orderBy('invoice_id','desc')->paginate(20);
        $this->data['pageTitle'] = __('My Invoices');
        $this->data['paginator'] = $invoices;
        return $this->data;
    }

    public function payinvoiceAction(){
        $id = $this->params('id');
        $invoice = Invoice::find($id);
        if($invoice && $invoice->student_id==$this->getId()){
            $cart = unserialize($invoice->cart);
            $cart->store();
            return $this->redirect()->toRoute('cart');
        }
    }

    public function surveysAction()
    {
        // TODO Auto-generated NewsController::indexAction() default action
        $table = new SurveyTable($this->getServiceLocator());
        $testQuestionTable = new SurveyQuestionTable($this->getServiceLocator());
        $studentTestTable = new SurveyResponseTable($this->getServiceLocator());

        $paginator = $table->getStudentRecords($this->getId());

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Surveys'),
            'studentTest'=>$studentTestTable,
            'questionTable'=>$testQuestionTable
        ));

    }
}