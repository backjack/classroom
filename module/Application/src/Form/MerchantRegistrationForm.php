<?php

namespace Site\Form;


use Zend\Form\Element\Captcha;
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Intermatics\BaseForm;

class MerchantRegistrationForm extends BaseForm {
	
	
	function __construct($serviceLocator,CaptchaAdapter $captchaAdapter = null) {
		parent::__construct('MerchantRegistration');
		$this->setAttribute('method', 'post');
		$this->setServiceLocator($serviceLocator);
	
		$fields = array(
				'merchant_firstname'=>array('type'=>'text','label'=>__('Firstname'),'required'=>true),
				'merchant_lastname'=>array('type'=>'text','label'=>__('Lastname'),'required'=>true),
				'merchant_tel'=>array('type'=>'text','label'=>__('Telephone Number')),
				'merchant_email'=>array('type'=>'text','label'=>__('Email'),'required'=>true),
				'merchant_password'=>array('type'=>'password','label'=>__('Password'),'required'=>true),
				'confirm_password'=>array('type'=>'password','label'=>__('Confirm Password'),'required'=>true),
				'username'=>array('type'=>'text','label'=>__('Username'),'required'=>true)
		);
	
		$this->createElements($fields); 
		
		//add captcha
		
		$captcha = new Captcha('captcha');
        $captcha->setCaptcha($captchaAdapter);
        $captcha->setOptions(array('label' => __('captcha-label')));
        $this->add($captcha);
		
		$csrf = new Element\Csrf('security');
		
		
		 $this->add($csrf);
		
		
		
	
	}
	
	
}

?>