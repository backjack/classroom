<?php

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Form\ProfileFilter;
use Application\Form\ProfileForm;
use Intermatics\HelperTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\AccountsTable;

/**
 * AccountController
 *
 * @author
 *
 * @version
 *
 */
define('DIR_MER_IMAGE', 'public/');
class AccountController extends AbstractController {
	/**
	 * The default action - show the home page
	 */
	use HelperTrait;
	protected $storage;
	protected $authservice;
	public function getAuthService()
	{
	
		return $this->getServiceLocator()->get('AdminAuthService');
	}
	
	public function indexAction() {
		// TODO Auto-generated AccountController::indexAction() default action
		
		return new ViewModel ();
	}
	
	public function emailAction()
	{
		
		$output = array();
		$identity= $this->getAuthService()->getIdentity();
		$email = $identity['email'];
		$accountsTable =new AccountsTable($this->getServiceLocator());
		 
		if ($this->request->isPost()) {
			$post = $this->request->getPost();
			
			$newEmail = $post['email'];
			$accountsTable->tableGateway->update(array('email'=>$newEmail),array('email'=>$email));
			$output['message']='Email changed to '.$newEmail;
			$this->getAuthService()->getStorage()->write(array(
					'email'=>$newEmail,
					'role'=>'admin'
			));
			
		}
		$output['pageTitle']=__('Change Your Email');
		return $output;
	}
	
	
	public function passwordAction()
	{
		$output = array();
		$identity= $this->getAuthService()->getIdentity();
		$email = $identity['email'];
		 
		$accountsTable =new AccountsTable($this->getServiceLocator());
		if ($this->request->isPost()) {
			$post = $this->request->getPost();
			if ($post['password']==$post['confirm_password']) {
				
			$newPassword = md5(trim($post['password']));
			$accountsTable->tableGateway->update(array('password'=>$newPassword),array('email'=>$email));
			$output['message']=__('Password changed!');
			
			}
			else{
				$output['message']=__('password-not-match');
			}	
		}
		$output['pageTitle']=__('Change Your Password');
		return $output;
	}
	
	
	
	public function profileAction()
    {

        $output = array();
        $accountsTable =new AccountsTable($this->getServiceLocator());
        $row = $this->getAdmin();
        $form = new ProfileForm(null,$this->getServiceLocator());
        $filter = new ProfileFilter();
        $form->setInputFilter($filter);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($post);

            if($form->isValid()){
                $data = $form->getData();
                $accountsTable->update($data,$row->account_id);
                $output['message']=__('Changes Saved!');
            }
           else{

               $output['message']=__('Submission Failed');
           }

        }
        else{

            $form->setData([
                'first_name'=>$row->first_name,
                'last_name'=>$row->last_name,
                'notify'=>$row->notify,
                'account_description'=>$row->account_description
            ]);
        }

        if ($row->picture && file_exists(DIR_MER_IMAGE . $row->picture) && is_file(DIR_MER_IMAGE . $row->picture)) {
            $output['display_image'] = resizeImage($row->picture, 100, 100,$this->getBaseUrl());
        } else {
            $output['display_image'] = resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }


        $output['no_image']= $this->getBaseUrl().'/'.resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());


        $output['form'] = $form;
        $output['pageTitle']=__('Profile');
        return $output;
    }

	
}