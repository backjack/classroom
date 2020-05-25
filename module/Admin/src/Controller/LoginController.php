<?php
namespace Admin\Controller;
 
use Application\Controller\AbstractController;
use Application\Entity\Account;
use Application\Form\PasswordResetFilter;
use Application\Form\PasswordResetForm;
use Application\Model\AccountsTable;
use Application\Model\PasswordResetTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;

 
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
    public function getAuthService()
    { 
        
        return $this->getServiceLocator()->get('AdminAuthService');
    }
        
    public function logoutAction()
    {
    	 
        $this->getAuthService()->clearIdentity();
        $sessionManager = $this->getServiceLocator()->get(SessionManager::class);
        $sessionManager->forgetMe();
        $sessionManager->expireSessionCookie();
      UtilityFunctions::setRole('guest');
        return $this->redirect()->toRoute('admin/signin' , array( 
                        'controller' => 'login',
                        'action' =>  'index'
                    ));
    }

    public function indexAction()
    {
        $session = new Container('admin_login');
        //check for redirect
        $redirect = $this->params()->fromQuery('redirect');
        if(!empty($redirect)){
            $session->url = $redirect;
        }

    	$role = UtilityFunctions::getRole();
    	if ($role=='admin') {
    		return $this->redirect()->toRoute('admin/default' , array(
    				'controller' => 'index',
    				'action' =>  'index'
    		));
    	}

		$form = new LoginForm();

		$overide = $this->params()->fromQuery('ov');


		$viewModel  = new ViewModel(array('ov'=>$overide,'form' => $form,'pageTitle'=>'Login','subTitle'=>'Login to your admin area'));
		//$this->layout('layout/layout');
		return $viewModel;
    }

    public function processAction()
    {
       $accountsTable = new AccountsTable($this->getServiceLocator());



        $session = new Container('admin_login');




        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('admin/signin');
        }

        $post = $this->request->getPost();



        $form = new LoginForm();
		$inputFilter = new LoginFilter();
		$form->setInputFilter($inputFilter);

        $form->setData($post);
        if (!$form->isValid()) {

            $model = new ViewModel(array(
                'loginerror' => true,
                'message'=>$this->getFormErrors($form),
                'form'  => $form,
                'pageTitle'=>__('Login'),'subTitle'=>__('admin-login-prompt')
            ));
            $model->setTemplate('admin/login/index');

            return $model;
        } else {


        	//check if rememberme is checked

            $sessionManager = $this->getServiceManager()->get(SessionManager::class);
        	// $sessionManager = Container::getDefaultManager();
            /*
        	if($this->request->getPost('rememberme')==1)
        	{
       			$sessionManager->rememberMe();
        	}
*/

        	 try{
                 $email = trim($this->request->getPost('email'));

                  if($accountsTable->emailExists($email)){
                      $account = $accountsTable->getAccountWithEmail($email);
                      if($account->account_status != 1){
                          throw new \Exception(__('account-is-inactive'));
                      }
                  }



        	 	//check authentication...
 			$this->getAuthService()->getAdapter()
								   ->setIdentity($this->request->getPost('email'))
								   ->setCredential(md5($this->request->getPost('password')));
            $result = $this->getAuthService()->authenticate();



            if ($result->isValid()) {
            	 UtilityFunctions::setRole('admin');
				$this->getAuthService()->getStorage()->write(array(
						'email'=>$this->request->getPost('email'),
						'role'=>'admin'
				));


                if($this->request->getPost('rememberme')==1)
                {
                    $sessionManager->rememberMe();
                }

                //log user


                if(isset($session->url) && !empty($session->url) && !preg_match('#redirect#',$session->url)  && !preg_match('#signin#',$session->url) ){
                    $landing = $session->url;


                    unset($_SESSION['admin_login']);

                    return $this->redirect()->toUrl($landing);
                }
				 
					return $this->redirect()->toRoute('admin/default' , array( 
									'controller' => 'index', 
									'action' =>  'index' 
								));	
				 
            } else {
				$model = new ViewModel(array(
					'loginerror' => true,
					'form'  => $form,
                    'message'=>__('invalid-email-password'),
					'pageTitle'=>__('Login'),'subTitle'=>__('login-admin-area')
				));
				$model->setTemplate('admin/login/index');
				return $model;
			}
			
        	 }
        	 catch(\Exception $ex)
        	 {

        	 	$model = new ViewModel(array(
        	 			'loginerror' => true,
        	 			'form'  => $form,
                        'message'=>$ex->getMessage(),
        	 			'pageTitle'=>__('Login'),'subTitle'=>__('login-admin-area')
        	 	));
        	 	$model->setTemplate('admin/login/index');
        	 	return $model;
        	 }
			
			
			
			
			
			
			
			
			
			
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
	
  
    public function resetoldAction()
    {
        $form = new BaseForm('reset');
        $form->createText('email','Email',__('Your email address'));
        $form->addCSRF();
        $output = array();
        $accountTable = new AccountsTable($this->getServiceLocator());
        if($this->request->isPost()){

            $form->setData($this->request->getPost());
            if($form->isValid()){
                $post = $this->request->getPost();
                $email = trim($post['email']);
                if($accountTable->emailExists($email)){
                    $row = $accountTable->getAccountWithEmail($email);
                    $password = substr(md5(strtolower(time().$email)),0,5);

                    $accountTable->update(array('password'=>md5($password)),$row->account_id);

                    $mode = getenv('APP_MODE');
                    if($mode != 'demo'){

                        $title = __('Password Reset');
                        $senderName = $this->getSetting('general_site_name');
                        $firstName = $row->first_name;
                        $recipientEmail = $email;

                        $message = __('password-reset-mail',['firstName'=>$firstName,'password'=>$password,'senderName'=>$senderName]);

                        $this->sendEmail($recipientEmail,$title,$message);

                    }
                    $output['message']=__('password-reset-alert');

                }
                else{
                    $output['message']=__('no-email-assoc',['email'=>$email]);
                }
            }
            else{
                $output['message']=__('submission-failed-try-again');
            }

        }
        $output['form'] = $form;

        return $output;
    }


    /**
     * @return array
     * @throws \Exception
     * This resets a user's password
     */
    public function resetAction()
    {

        $output = array();
        $accountsTable = new AccountsTable($this->getServiceLocator());
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
                if($accountsTable->emailExists($email)){
                    $row = $accountsTable->getAccountWithEmail($email);

                    $token = $resetTable->addEntry($email);
                    $url = $this->getBaseUrl().'/admin/change-password?token='.$token;

                    $mode = getenv('APP_MODE');
                    if($mode != 'demo'){

                        $title = __('Password Reset');
                        $senderName = $this->getSetting('general_site_name',null,'TrainEasy');
                        $firstName = $row->first_name;
                        $recipientEmail = $email;

                        $message = "
                    Dear $firstName, <br/>
                    You have requested to change your password. Please click this url to change it now <br/>
                    <a href=\"$url\">$url</a> <br/>
                    Note: If you did not make this request, please ignore this email. <br/>
                    $senderName
                    ";
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
        return $output;
    }

    public function changepasswordAction(){
        $accountsTable = new AccountsTable($this->getServiceLocator());
        $resetTable = new PasswordResetTable($this->getServiceLocator());
        $form = new PasswordResetForm($this->getServiceLocator());
        $form->addCSRF();
        $filter = new PasswordResetFilter();

        $token = $this->params()->fromQuery('token');
        $message = null;
        $row = $resetTable->validToken($token);
        if ($row) {

            $accountRow = $accountsTable->getAccountWithEmail($row->email);

            if ($this->request->isPost()) {

                $formData = $this->request->getPost();
                $form->setInputFilter($filter);

                $form->setData($formData);

                if ($form->isValid()) {

                    $data = $form->getData();

                    $password = $data['password'];
                    $accountsTable->update(array('password'=>md5($password)), $accountRow->account_id);
                    $resetTable->deleteTokenRecords($token);
                    $form = new PasswordResetForm($this->getServiceLocator());
                    $this->flashMessenger()->addMessage(__('pwchanged-msg1'));
                    $this->redirect()->toRoute('admin/signin');
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
            $message = __('Invalid Token');
        }

        $viewModel  = new ViewModel(array('form' => $form,'message'=>$message,'display'=>$display,'pageTitle'=>__('Change Your Password'),'subTitle'=>__('reset-pwd-here'),'token'=>$token));

        return $viewModel;
    }

    private function getPageAsync($url, $params = array('noparam'=>'true'), $type='GET')
    {
        //echo "Attempting to get $url <br/>";


        foreach ($params as $key => $val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        $fp = fsockopen($parts['host'],
            isset($parts['port'])?$parts['port']:80,
            $errno, $errstr, 30);

        $cusParams = $parts['query'];

        // Data goes in the path for a GET request
        if('GET' == $type) $parts['path'] .= '?'.$cusParams;

        $out = "$type ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        // Data goes in the request body for a POST request
        if ('POST' == $type && isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }



}
