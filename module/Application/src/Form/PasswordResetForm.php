<?php

namespace Application\Form;

use Zend\Form\Element\Captcha;
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Intermatics\BaseForm;

class PasswordResetForm extends BaseForm {
	
	
	function __construct($serviceLocator,CaptchaAdapter $captchaAdapter = null) {
		parent::__construct('Password Reset');
		$this->setAttribute('method', 'post');
		$this->setServiceLocator($serviceLocator);
	    $this->addCSRF();
		$fields = array(
				'password'=>array('type'=>'password','label'=>'Password','required'=>true,'placeholder'=>'Enter your password'),
				'confirm_password'=>array('type'=>'password','label'=>'Confirm Password','required'=>true),
		 );
	
		$this->createElements($fields);
	
		 
	
	
	
	
	}
	
	
}

?>