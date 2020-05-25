<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class AccountFilter extends InputFilter {
	function __construct() {
		
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
			'name'=>'first_name',
			'required'=>true,
			'validators'=>array(
			array(
				'name'=>'NotEmpty'
			)
		)
		));
		
		$this->add(array(
			'name'=>'last_name',
			'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));


        $this->add(array(
            'name'=>'role_id',
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

        ));


        $this->add(array(
            'name'=>'confirm_password',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    )
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