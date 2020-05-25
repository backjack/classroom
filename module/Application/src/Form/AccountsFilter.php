<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class AccountsFilter extends InputFilter {
	function __construct() {
		
		$this->add(array(
				'name'=>'username',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		$this->add(array(
				'name'=>'password',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		$this->add(array(
				'name'=>'account_type',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		$this->add(array(
				'name'=>'access_level',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		$this->add(array(
				'name'       => 'email',
				'required'   => true,
				'validators' => array(
						array(
								'name'    => 'EmailAddress',
								'options' => array(
										'domain' => true,
								),
						),
				),
		));
		
		$this->add(array(
			'name'=>'firstname',
			'required'=>true,
			'validators'=>array(
			array(
				'name'=>'NotEmpty'
			)
		)
		));
		
		$this->add(array(
			'name'=>'lastname',
			'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));


        $this->add(array(
            'name'=>'account_description',
            'required'=>false,
        ));


	}
}

?>