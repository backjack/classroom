<?php
// filename : module/Users/src/Users/Form/RegisterForm.php
namespace Application\Form;

use Intermatics\BaseForm;
use Zend\Form\Form;

class LoginForm extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct('Login');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');
        $this->addCSRF();
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
				'required' => 'required' 
            ),
            'options' => array(
                'label' => __('Email'),
            ),
        )); 
        
	$this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
				'required' => 'required'                 
            ),
            'options' => array(
                'label' => __('Password'),
            ),
        )); 
	
	$this->add(array(
			'name'=>'rememberme',
			'type' => 'Zend\Form\Element\Checkbox',
			'options'=>array('label'=>__('Remember Me'),'checked_value'=>1,'unchecked_value'=>0),
	));
		


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => __('Login')
            ),
        )); 
    }
}
