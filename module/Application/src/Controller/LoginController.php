<?php
namespace Application\Controller;
 
use Application\Form\PasswordResetFilter;
use Application\Form\PasswordResetForm;
use Application\Form\SignupFilter;
use Application\Form\SignupForm;
use Application\Model\PasswordResetTable;
use Application\Model\RegistrationFieldTable;
use Application\Model\SessionTable;
use Application\Model\StudentFieldTable;
use Application\Model\StudentRegistrationTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTable;
use Hybridauth\Hybridauth;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\Opencart\Library\Mail;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;
use Zend\Form\Element;

 
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\LoginForm;
use Application\Form\LoginFilter; 
use Intermatics\UtilityFunctions; 
use Zend\Session\Container;
use Intermatics\BaseController; 
use Intermatics\Mailer;  

class LoginController extends AbstractController
{
    use HelperTrait;
	protected $storage;
    protected $authservice;
    protected $uploadDir;

    public function __construct(){
        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $filePath = 'usermedia'.$user;
        $this->uploadDir = $filePath.'/student_uploads/'.date('Y_m');
    }

    private function makeUploadDir(){
        $path = 'public/'.$this->uploadDir;
        if(!file_exists($path)){
            mkdir($path,0777,true);
        }
    }

    public function getAuthService()
    { 
        
        return $this->getServiceLocator()->get('StudentAuthService');
    }

    /**
     * @return \Zend\Http\Response
     * This logs a user out
     */
    public function logoutAction()
    {
    	 
        $this->getAuthService()->clearIdentity();
      UtilityFunctions::setRole('guest');

        session_destroy();
        return $this->redirect()->toRoute('application/signin' , array( 
                        'controller' => 'login', 
                        'action' =>  'index' 
                    ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * This displays the login form
     */
    public function indexAction()
    {

        //check for redirect
        $redirect = $this->params()->fromQuery('redirect');
        if(!empty($redirect)){
            $session = new Container('student_login');
            $session->url = $redirect;
        }
      
    	$role = UtilityFunctions::getRole();
    	if ($role=='student') {
    		return $this->redirect()->toRoute('application/dashboard');
    	}
    	
		$form = new LoginForm();
		
		$overide = $this->params()->fromQuery('ov');
		
	/*	$registerModel = $this->forward()->dispatch('Application\Controller\Login',['action'=>'register']);
        $variables = $registerModel->getVariables();
        dd($variables);*/

		$viewModel  = $this->getViewModel(array('ov'=>$overide,'form' => $form,'pageTitle'=>__('Login'),'subTitle'=>__('admin-login-prompt')));
		//$this->layout('layout/layout'); 
		$viewModel->setTerminal(true);
        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * this processes the login
     */
    public function processAction()
    {
       

    	$session = new Container('student_login');
    	


 
        if (!$this->request->isPost()) {

            return $this->redirect()->toRoute('application/signin');
        }
         
        $post = $this->request->getPost();
        


        $form = new LoginForm();
		$inputFilter = new LoginFilter();
		$form->setInputFilter($inputFilter);

        $form->setData($post);
        if (!$form->isValid()) {
            $this->flashMessenger()->addMessage($this->getFormErrors($form));
            return $this->redirect()->toRoute('application/signin');

        } else {
        	
        	//check if rememberme is checked
        	$sessionManager= $this->getServiceLocator()->get('Zend\Session\SessionManager');
        	 //$sessionManager = Container::getDefaultManager();
        	if($this->request->getPost('rememberme')==1)
        	{
       			$sessionManager->rememberMe();
        	}
        	
        	 try{


                 $email = trim($this->request->getPost('email'));
                 $studentTable = new StudentTable($this->getServiceLocator());
                 if($studentTable->emailExists($email))
                 {
                     $student = $studentTable->getStudentWithEmail($email);
                     if(empty($student->status)){
                         $this->flashmessenger()->addMessage(__('account-is-inactive'));
                         return $this->redirect()->toRoute('application/signin');
                     }
                 }


        	 	//check authentication...
			$this->getAuthService()->getAdapter()
								   ->setIdentity(trim($this->request->getPost('email')))
								   ->setCredential(md5($this->request->getPost('password')));
            $result = $this->getAuthService()->authenticate();
            
          
             
            if ($result->isValid()) {



            	 UtilityFunctions::setRole('student');



				$this->getAuthService()->getStorage()->write(array(
						'email'=>trim($this->request->getPost('email')),
						'role'=>'student'
				));

                //check if a session is avalable
                $enrollSession = new Container('enroll');
                if(isset($enrollSession->id) && !empty($enrollSession->id)){
                    return $this->redirect()->toRoute('application/payment');
                }

                if(isset($session->url) && !empty($session->url) && !preg_match('#redirect#',$session->url) ){
                    $landing = $session->url;

                    unset($_SESSION['student_login']);

                    return $this->redirect()->toUrl($landing);
                }

                return   $this->redirect()->toRoute('application/dashboard');
				 
            } else {
                $this->flashMessenger()->addMessage(__('login-failed'));
                return $this->redirect()->toRoute('application/signin');

			}
			
        	 }
        	 catch(\Exception $ex)
        	 {
        	   // exit($ex->getMessage().$ex->getTraceAsString());
                 $this->flashMessenger()->addMessage(__('login-failed'));
                 return $this->redirect()->toRoute('application/signin');


        	 }
			
			
			
			
			
			
			
			
			
			
		}
    }

    public function activateAction(){
        $studentRegistrationTable = new StudentRegistrationTable($this->getServiceLocator());
        $code = $this->params()->fromQuery('code');
        $code = trim($code);
        $row = $studentRegistrationTable->getCodeRecord($code);
        if($row){
            $data = unserialize($row->data);
            try{
                $this->addStudent($data);
                $studentRegistrationTable->deleteCodeRecord($code);
                $this->flashMessenger()->addMessage(__('account-activated'));
                $this->redirect()->toRoute('application/dashboard');
            }
            catch(\Exception $ex){
                $viewModel = new ViewModel(['pageTitle'=>__('Invalid URL'),'content'=>__('link-not-valid').'<br/>'.$ex->getMessage()]);
                $viewModel->setTemplate('application/index/page');
                return $viewModel;
            }
        }
        else{
            $viewModel = new ViewModel(['pageTitle'=>__('Invalid URL'),'content'=>__('link-not-valid')]);
            $viewModel->setTemplate('application/index/page');
            return $viewModel;
        }

    }

    public function confirmAction()
    {
         
		$user_email = $this->getAuthService()->getStorage()->read();
		$viewModel  = new ViewModel(array(
            'user_email' => $user_email 
        )); 
		return $viewModel; 
    }

    /**
     * @return array
     * @throws \Exception
     * This resets a user's password
     */
    public function resetAction()
    {
        $output = array();
    	$studentTable = new StudentTable($this->getServiceLocator());
        $resetTable = new PasswordResetTable($this->getServiceLocator());
        $form = new BaseForm('reset');
        $form->createText('email','Email',__('Your email address'));
        $form->addCSRF();

        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid())
            {
                $post = $form->getData();
                $email = trim($post['email']);
                if($studentTable->emailExists($email)){
                    $row = $studentTable->getStudentWithEmail($email);

                    $token = $resetTable->addEntry($email);
                    $url = $this->getBaseUrl().'/student/change-password?token='.$token;

                    $mode = getenv('APP_MODE');
                    if($mode != 'demo'){

                        $title = 'Password Reset';
                        $senderName = $this->getSetting('general_site_name');
                        $firstName = $row->first_name;
                        $recipientEmail = $email;

                        $message = __('password-reset-link-mail',['firstName'=>$firstName,'url'=>$url,'senderName'=>$senderName]);

                        $this->sendEmail($recipientEmail,$title,$message);
                    }
                    $output['message']=__('sent-resent-link-msg');

                }
                else{
                    $output['message']=__('no-email-assoc',['email'=>$email]);
                }
            }
            else{
                $output['message']=__('submission-failed-try-again');
            }






        }
        $output['pageTitle'] = __('Reset your password');
        $output['form'] = $form;
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function changepasswordAction(){
        $studentsTable = new StudentTable($this->getServiceLocator());
        $resetTable = new PasswordResetTable($this->getServiceLocator());
        $form = new PasswordResetForm($this->getServiceLocator());
        $filter = new PasswordResetFilter();

        $token = $this->params()->fromQuery('token');
        $message = null;
        $row = $resetTable->validToken($token);
        if ($row) {
            
            $studentRow = $studentsTable->getStudentWithEmail($row->email);
            
            if ($this->request->isPost()) {

                $formData = $this->request->getPost();
                $form->setInputFilter($filter);

                $form->setData($formData);

                if ($form->isValid()) {

                    $data = $form->getData();

                    $password = $data['password'];
                    $studentsTable->update(array('password'=>md5($password)), $studentRow->student_id);
                    $resetTable->deleteTokenRecords($token);
                    $form = new PasswordResetForm($this->getServiceLocator());
                    $this->flashMessenger()->addMessage(__('pwchanged-msg1'));
                    $this->redirect()->toRoute('application/signin');
                    $message = __('pwchanged-msg2');
                }
                else{
                    $message = __('pwchanged-msg3');
                }

            }




            $display = true;
        }
        else{
            $display = false;
        }

        $viewModel  = new ViewModel(array('form' => $form,'message'=>$message,'display'=>$display,'pageTitle'=>__('Change Your Password'),'subTitle'=>__('reset-pwd-here'),'token'=>$token));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     * This handles the registration. It checks if registration is enabled
     */
    public function registerAction()
    {
        $session = new Container('student_login');
        $role = UtilityFunctions::getRole();
        $regStatus = $this->getSetting('regis_enable_registration',$this->getServiceLocator());
        if($regStatus!=1){
            $output = ['content'=>__('registration-is-disabled')];
            if(!isset($_GET['homepage'])){
                $output['pageTitle'] = __('Registration Disabled');
            }

            $viewModel = new ViewModel($output);
            $viewModel->setTemplate('application/index/page');
            return $viewModel;
        }

        if($this->getServiceLocator()->get('StudentAuthService')->hasIdentity() && $role=='student'){
            if(isset($_GET['homepage'])){
                $viewModel = new ViewModel(['pageTitle'=>'','content'=>__('already-logged-in')]);
                $viewModel->setTemplate('application/index/page');
                return $viewModel;
            }
            else{
                return   $this->redirect()->toRoute('application/dashboard');
            }

        }



        $output = array();
        $studentsTable = new StudentTable($this->getServiceLocator());
        $studentRegistrationTable  = new StudentRegistrationTable($this->getServiceLocator());
        $captchaService = $this->getServiceLocator()->get('SanCaptcha');
        $form = new SignupForm(null,$this->getServiceLocator(),$captchaService);
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldTable = new StudentFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getActiveFields();
        $filter = new SignupFilter($this->getServiceLocator());
        $config = $this->getServiceLocator()->get('config');
        $config = $config['site'];


        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();

            if(getSetting('regis_captcha_type')=='google'){

                $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
                $recaptcha_secret = getSetting('regis_recaptcha_secret');
                $recaptcha_response = $data['captcha'];

                // Make and decode POST request:
            /*    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
                $recaptcha = json_decode($recaptcha);

*/




                $curl = curl_init();

                $captcha_verify_url = "https://www.google.com/recaptcha/api/siteverify";

                curl_setopt($curl, CURLOPT_URL,$captcha_verify_url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=".$recaptcha_secret."&response=".$recaptcha_response);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $captcha_output = curl_exec($curl);

                curl_close ($curl);

                $recaptcha = json_decode($captcha_output);









                // Take action based on the score returned:
             try{
                    if ($recaptcha->score < 0.5) {
                        // Verified - send email
                        $this->flashMessenger()->addMessage(__('invalid-captcha'));
                        return $this->redirect()->toUrl(selfURL());
                    }
                }
                catch(\Exception $ex){
                    $this->flashMessenger()->addMessage($ex->getMessage());
                    return $this->redirect()->toUrl(selfURL());
                }

            }

            $form->setData(array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            ));
            if ($form->isValid()  && !$studentsTable->emailExists($data['email']) ) {

                $data = $form->getData();

                //check for formatted phone number
                $formattedNo  = $this->request->getPost('fmobilenumber');
                if(!empty($formattedNo)){
                    $data['mobile_number'] = getPhoneNumber($this->purgeNumber($formattedNo));
                }

                if($this->getSetting('regis_confirm_email')==1){

                    $code = md5($data['email'].time().rand(10000,9999999)).md5($data['email'].time().rand(10000,9999999));
                    $code = substr($code, 0,200);

                    $dbData = [
                      'created_on'=>time(),
                        'data'=> serialize($data),
                        'code'=> $code
                    ];

                    $studentRegistrationTable->addRecord($dbData);
                    $url = $this->getBaseUrl().'/confirm?code='.$code;

                    $title = __('email-confirmation');
                    $senderName = $this->getSetting('general_site_name',$this->getServiceLocator());
                    $firstName = $data['first_name'];
                    $recipientEmail = $data['email'];
                    $message = __('activate-mail',['firstName'=>$firstName,'senderName'=>$senderName,'url'=>$url]);

                    $this->sendEmail($recipientEmail,$title,$message);
                    $this->redirect()->toRoute('confirm_email');
                }
                else{
                    $this->addStudent($data);

                    if(isset($session->url) && !empty($session->url) && !preg_match('#redirect#',$session->url) ){
                        $landing = $session->url;

                        unset($_SESSION['student_login']);

                        return $this->redirect()->toUrl($landing);
                    }

                    return $this->redirect()->toRoute('application/dashboard');
                }



            }
            elseif($studentsTable->emailExists($data['email'])){
                $output['message'] = __('regis-fail-msg1',['email'=>$data['email']]);
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
                $output['message'] = __('regis-fail-msg2').$fields;
            }

        }

        $output['form'] = $form;
        $output['pageTitle']= __('Register');
        $output['action'] = 'add';
        $output['id']=0;
        $viewModel= new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;

    }

    public function registerwidgetAction()
    {
        $role = UtilityFunctions::getRole();
        $regStatus = $this->getSetting('regis_enable_registration',$this->getServiceLocator());
        if($regStatus!=1){
            $output = ['content'=>__('registration-is-disabled')];
            if(!isset($_GET['homepage'])){
                $output['pageTitle'] = __('Registration Disabled');
            }

            $viewModel = new ViewModel($output);
            $viewModel->setTemplate('application/index/page');
            return $viewModel;
        }

        if($this->getServiceLocator()->get('StudentAuthService')->hasIdentity() && $role=='student'){
            if(isset($_GET['homepage'])){
                $viewModel = new ViewModel(['pageTitle'=>'','content'=>__('already-logged-in')]);
                $viewModel->setTemplate('application/index/page');
                return $viewModel;
            }
            else{
                return   $this->redirect()->toRoute('application/dashboard');
            }

        }



        $output = array();
        $studentsTable = new StudentTable($this->getServiceLocator());
        $studentRegistrationTable  = new StudentRegistrationTable($this->getServiceLocator());
        $captchaService = $this->getServiceLocator()->get('SanCaptcha');
        $form = new SignupForm(null,$this->getServiceLocator(),$captchaService);
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldTable = new StudentFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getActiveFields();
        $filter = new SignupFilter($this->getServiceLocator());
        $config = $this->getServiceLocator()->get('config');
        $config = $config['site'];


        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();

            $form->setData(array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            ));
            if ($form->isValid()  && !$studentsTable->emailExists($data['email']) ) {

                $data = $form->getData();

                if($this->getSetting('regis_confirm_email')==1){

                    $code = md5($data['email'].time().rand(10000,9999999)).md5($data['email'].time().rand(10000,9999999));
                    $code = substr($code, 0,200);

                    $dbData = [
                        'created_on'=>time(),
                        'data'=> serialize($data),
                        'code'=> $code
                    ];

                    $studentRegistrationTable->addRecord($dbData);
                    $url = $this->getBaseUrl().'/confirm?code='.$code;

                    $title = 'Email Confirmation';
                    $senderName = $this->getSetting('general_site_name',$this->getServiceLocator());
                    $firstName = $data['first_name'];
                    $recipientEmail = $data['email'];

                    $message = __('activate-mail',['senderName'=>$senderName,'url'=>$url,'firstName'=>$firstName]);

                    $this->sendEmail($recipientEmail,$title,$message);
                    $this->redirect()->toRoute('confirm_email');
                }
                else{
                    $this->addStudent($data);
                    return $this->redirect()->toRoute('application/dashboard');
                }



            }
            elseif($studentsTable->emailExists($data['email'])){
                $output['message'] = __('regis-fail-msg1',['email'=>$data['email']]);
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
                $output['message'] = __('regis-fail-msg2').$fields;
            }

        }

        $output['form'] = $form;
        $output['pageTitle']= __('Register');
        $output['action'] = 'add';
        $output['id']=0;
        $viewModel= new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;

    }

    public function confirmmailAction(){

        $viewModel = new ViewModel(['pageTitle'=>__('Email Confirmation'),'content'=>__('email-confirmation-msg')]);

        $viewModel->setTemplate('application/index/page');
        return $viewModel;
    }

    private function addStudent($data,$createStudent=true){
        $output = array();
        $studentsTable = new StudentTable($this->getServiceLocator());
        $captchaService = $this->getServiceLocator()->get('SanCaptcha');
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $studentFieldTable = new StudentFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getActiveFields();
         $config = $this->getServiceLocator()->get('config');
        $config = $config['site'];

        $data = removeTags($data);
        if($createStudent){
            $array = [
                'first_name'=>$data['first_name'],
                'last_name'=>$data['last_name'],
                'mobile_number'=>getPhoneNumber($data['mobile_number']),
                'email'=>$data['email'],
                'password'=>$data['password']
            ];

            $array['first_name'] = ucwords(strtolower($array['first_name']));
            $array['last_name'] = ucwords(strtolower($array['last_name']));
            $array['email'] = strtolower($array['email']);

            $array[$studentsTable->getPrimary()]=0;
            $array['password'] = md5($array['password']);
            if(isset($array['confirm_password'])){
                unset($array['confirm_password']);
            }

            if(isset($array['captcha'])){
                unset($array['captcha']);
            }

            $studentId = $studentsTable->saveRecord($array);
        }
        else{
            $studentRecord = $studentsTable->getStudentWithEmail($data['email']);
            $studentId = $studentRecord->student_id;

            if(!empty($data['mobile_number'])){
                $studentsTable->update(['mobile_number'=>getPhoneNumber($data['mobile_number'])],$studentId);
            }
        }


        $fields= $registrationFieldsTable->getActiveFields();
        foreach($fields as $row){
            $value = $data['custom_'.$row->registration_field_id];

            if($row->type != 'file'){

                $studentFieldTable->saveField($studentId,$row->registration_field_id,$value);
            }
            elseif(!empty($value['name']) && file_exists($value['tmp_name'])){

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

        //enroll student to most recent session



        //    $this->flashmessenger()->addMessage('Changes saved!');
        $output['message'] = __('Record Added!');

        //send email
        $mode = getenv('APP_MODE');
        if($mode != 'demo' && $createStudent){

            $title = __('New Account Details');
            $senderName = $this->getSetting('general_site_name',$this->getServiceLocator());
            $firstName = $array['first_name'];
            $recipientEmail = $array['email'];
            $password = $data['password'];
            $siteUrl = $this->absoluteRoute('application/signin');

            $message = __('new-account-mail',['siteUrl'=>$siteUrl,'firstName'=>$firstName,'senderName'=>$senderName,'recipientEmail'=>$recipientEmail,'studentPassword'=>$password]);

            $this->sendEmail($recipientEmail,$title,$message);

            if($this->getSetting('regis_signup_alert')==1){
                $this->notifyAdmins(__('New registration'),$array['first_name'].' '.$array['last_name'].' '.__('just registered'));
            }

        }

        if($createStudent){

            $this->getAuthService()->getAdapter()
                ->setIdentity($data['email'])
                ->setCredential(md5($data['password']));
            $result = $this->getAuthService()->authenticate();

            UtilityFunctions::setRole('student');
            $this->getAuthService()->getStorage()->write(array(
                'email'=>$data['email'],
                'role'=>'student'
            ));
        }
        else{
            //update registration complete
            $studentsTable->update(['registration_complete'=>1],$this->getId());
        }


        if($this->getSetting('general_auto_enroll')==1)
        {
            $sessionTable = new SessionTable($this->getServiceLocator());
            $sessions = $sessionTable->getPaginatedRecords(false,null,true);
            $session = $sessions->current();
            if(!empty($session->session_id)){

                if(empty($session->payment_required))
                {
                    $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
                    $code = generateRandomString(5);
                    $studentSessionTable->addRecord(array(
                        'student_id'=>$studentId,
                        'session_id'=>$session->session_id,
                        'reg_code'=>$code,
                        'enrolled_on'=>time()
                    ));

                    $student = $this->getStudent();
                    $message = "<h4>".__('Your enrollment code is')." $code</h4>";
                    $emailMessage = $message.$this->getSetting('regis_email_message',$this->getServiceLocator());
                    $this->sendEmail($student->email,__('Enrollment Complete'),$emailMessage,$this->getServiceLocator());

                    return $this->redirect()->toRoute('application/dashboard');

                }
                else{
                    $enrollSession = new Container('enroll');
                    if(isset($enrollSession->id) && !empty($enrollSession->id)){
                        return $this->redirect()->toRoute('application/payment');
                    }
                }


            }


        }

    }


    public function socialAction(){

        $session = new Container('student_login');

        $network = $this->params()->fromQuery('network');
        //create config
        $config = array('callback'=> $this->getBaseUrl().'/student/social-login?network='.$network);

        if ($this->getSetting('social_enable_facebook')==1) {
            $config['providers']['Facebook']=  array (
                "enabled" => true,
                "keys"    => array ( "id" => trim($this->getSetting('social_facebook_app_id')), "secret" => trim($this->getSetting('social_facebook_secret')) ),
                "scope"   => "email",
                "trustForwarded" => false
            );
        }

        if ($this->getSetting('social_enable_google')==1) {
            $config['providers']['Google']=  array (
                "enabled" => true,
                "keys"    => array ( "id" => trim($this->getSetting('social_google_id')), "secret" => trim($this->getSetting('social_google_secret')) ),

            );
        }

        $config['debug_mode']=true;
        $config['debug_file']='hybridlog.txt';



        try{



            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybridauth( $config );

            // try to authenticate the user with twitter,
            // user will be redirected to Twitter for authentication,
            // if he already did, then Hybridauth will ignore this step and return an instance of the adapter
            $authSession = $hybridauth->authenticate($network);


            // get the user profile
            $userProfile = $authSession->getUserProfile();


            $email = $userProfile->email;
            $firstName= $userProfile->firstName;
            $lastName = $userProfile->lastName;
            $photoUrl = $userProfile->photoURL;
            $gender = $userProfile->gender;
            $phone = $userProfile->phone;
            if (empty($phone)) {
                $phone= 0;
            }

            //check if user already has account
            $studentTable = new StudentTable($this->getServiceLocator());
            if($studentTable->activeEmailExists($email)){

                //login student
                UtilityFunctions::setRole('student');
                $this->getAuthService()->getStorage()->write(array(
                    'email'=>$email,
                    'role'=>'student'
                ));




            }
            elseif($this->getSetting('regis_enable_registration')==1){


               $id =  $studentTable->addRecord([
                    'first_name'=>$firstName,
                    'last_name'=>$lastName,
                    'mobile_number'=>$phone,
                    'email'=>$email,
                    'status'=>1,
                    'password'=>substr(md5(time().$email.$firstName),0,6),
                    'student_created'=>time(),
                     'picture'=>$photoUrl,
                    'social_login'=>1,
                    'registration_complete'=>0
                ]);

                if(!empty($id)){

                    //login student
                    UtilityFunctions::setRole('student');
                    $this->getAuthService()->getStorage()->write(array(
                        'email'=>$email,
                        'role'=>'student'
                    ));
                }
                else{
                    return $this->goBack();
                }



            }
            else{
                $this->flashmessenger()->addMessage(__('login-failure'));
                return $this->goBack();
            }

            $sessionManager= $this->getServiceLocator()->get('Zend\Session\SessionManager');
            //$sessionManager = Container::getDefaultManager();
            $sessionManager->rememberMe();

            $student = $studentTable->getStudentWithEmail($email);

            //forward to account completion page if custom fields exist
            $registrationFieldTable = new RegistrationFieldTable($this->getServiceLocator());
            $fields = $registrationFieldTable->getActiveFields();
            if( ($fields->count()>0 && empty($student->registration_complete))  || empty($student->mobile_number)    ){
                return  $this->redirect()->toRoute('application/social-update');
            }
            else{
                //check if a session is available
                return $this->postLoginRedirect();
            }


        }
        catch(\Exception $ex){

            $this->flashmessenger()->addMessage(__('social-error'));
            return $this->goBack();
        }

    }

    private function postLoginRedirect(){
        $session = new Container('student_login');

        $enrollSession = new Container('enroll');
        if(isset($enrollSession->id) && !empty($enrollSession->id)){
            return $this->redirect()->toRoute('application/payment');
        }

        if(isset($session->url) && !empty($session->url) && !preg_match('#redirect#',$session->url) ){
            $landing = $session->url;

            unset($_SESSION['student_login']);

            return $this->redirect()->toUrl($landing);
        }

        return   $this->redirect()->toRoute('application/dashboard');
    }


    public function updateAction(){
        $output = [];
        $form = $this->getAccountUpdateForm();
        $student = $this->getStudent();

        if($this->request->isPost()){
            $formData = $this->request->getPost();

            $form->setData($formData);
            if($form->isValid()){
                $data= $form->getData();

                //check for formatted phone number
                $formattedNo  = $this->request->getPost('fmobilenumber');
                if(!empty($formattedNo)){
                    $data['mobile_number'] = getPhoneNumber($formattedNo);
                }


                $data['email'] = $student->email;
                $this->addStudent($data,false);
                return $this->postLoginRedirect();
            }
            else{
                $output['message'] = $this->getFormErrors($form);
            }
        }
        else{



            $form->setData([
               'mobile_number'=>empty($student->mobile_number)? '':$student->mobile_number
            ]);

        }
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $output['fields'] = $registrationFieldsTable->getActiveFields();
        $output['form'] = $form;
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    private function getAccountUpdateForm(){
        $form = new BaseForm();
        $form->addCSRF();

        $form->add(array(
            'name'=>'mobile_number',
            'attributes' => array(
                'type'=>'tel',
                'class'=>'form-control ',
                //  'placeholder'=>'Digits only',
                'pattern'=>'\d*',
                'x-autocompletetype'=>'tel'
            ),
            'options'=>array('label'=>__('Mobile Number'),),
        ));


        //create new form
        $registrationFieldsTable = new RegistrationFieldTable($this->getServiceLocator());
        $rowset = $registrationFieldsTable->getActiveFields();
        foreach($rowset as $row){

            switch($row->type){

                case 'checkbox':
                    $form->createCheckbox('custom_'.$row->registration_field_id,$row->name,1);
                    break;
                case 'radio':
                    $vals = nl2br($row->options);
                    $options = explode('<br />',$vals);

                    $selectOptions =[];
                    foreach($options as $value){
                        $selectOptions[$value]=$value;
                    }

                    $form->add(array(
                        'type' => 'Zend\Form\Element\Radio',
                        'name' => 'custom_'.$row->registration_field_id,
                        'options' => array(
                            'label' => $row->name,
                            'value_options' => $selectOptions,
                        )
                    ));
                    break;
                case 'text':
                    $form->createText('custom_'.$row->registration_field_id,$row->name,!empty($row->required),null,null,$row->placeholder);
                    break;
                case 'textarea':
                    $form->createTextArea('custom_'.$row->registration_field_id,$row->name,!empty($row->required),null,$row->placeholder);
                    break;
                case 'select':
                    $vals = nl2br($row->options);
                    $options = explode('<br />',$vals);

                    $selectOptions =[];
                    foreach($options as $value){
                        $selectOptions[$value]=$value;
                    }
                    $form->createSelect('custom_'.$row->registration_field_id,$row->name,$selectOptions,!empty($row->required));
                    break;
                case 'file':
                    $file = new Element\File('custom_'.$row->registration_field_id);
                    $file->setLabel($row->name)
                        ->setAttribute('id','custom_'.$row->registration_field_id);
                    $form->add($file);

            }

        }
        $form->setInputFilter($this->getAccountUpdateFilter());
        return $form;
    }

    private function getAccountUpdateFilter(){
        $filter = new InputFilter();
        $table= new RegistrationFieldTable($this->getServiceLocator());
        $rowset = $table->getActiveFields();

        $filter->add(array(
            'name'       => 'mobile_number',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'Digits',
                    'options' => array(
                        'domain' => true,
                    ),
                ),
            ),
        ));



        foreach($rowset as $row)
        {

            //validate file
            if($row->type=='file'){
                $input = new Input('custom_'.$row->registration_field_id);
                $input->setRequired(!empty($row->required));
                $input->getValidatorChain()
                    ->attach(new Size(5000000))
                    ->attach(new Extension('jpg,jpeg,png,gif,doc,docx,pdf'));


                $filter->add($input);
            }
            else{
                $form = array(
                    'name'=>'custom_'.$row->registration_field_id,
                    'required'=>!empty($row->required),
                );
                if(!empty($row->required)){
                    $form['validators'] = array(['name'=>'NotEmpty']);
                }
                $filter->add($form);
            }




        }

        return $filter;
    }

    private function purgeNumber($number){
        $number = str_ireplace('+undefined0','0',$number);
        $number = str_ireplace('+undefined','0',$number);
        return $number;
    }
}
